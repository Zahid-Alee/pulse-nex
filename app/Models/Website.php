<?php

// Add this scope to your Website model

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Website extends Model
{
    protected $fillable = [
        'name',
        'url',
        'check_interval', // in minutes
        'timeout',
        'status',
        'last_checked_at',
        'user_id'
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'check_interval' => 'integer',
        'timeout' => 'integer'
    ];

    /**
     * Scope to get websites that need to be checked
     * This considers the check_interval and last_checked_at to determine if a check is due
     */
    public function scopeNeedsCheck(Builder $query): Builder
    {
        $now = Carbon::now('UTC');

        return $query->where(function ($q) use ($now) {
            // Websites that have never been checked
            $q->whereNull('last_checked_at')
                // OR websites where enough time has passed since last check
                ->orWhere(function ($subQuery) use ($now) {
                    $subQuery->whereNotNull('last_checked_at')
                        ->whereRaw('TIMESTAMPDIFF(SECOND, last_checked_at, ?) >= check_interval', [$now]);
                });
        })
            ->where('check_interval', '>', 0) // Only check websites with valid intervals
            ->orderBy('last_checked_at', 'asc') // Check oldest first
            ->orderBy('check_interval', 'asc'); // Then prioritize shorter intervals
    }

    /**
     * Scope to get recent checks for statistics
     */
    public function scopeRecentChecks($query, int $days = 30)
    {
        $cutoffDate = Carbon::now('UTC')->subDays($days);

        return $this->hasMany(UptimeCheck::class)
            ->where('checked_at', '>=', $cutoffDate)
            ->orderBy('checked_at', 'desc');
    }

    /**
     * Relationship to uptime checks
     */
    public function uptimeChecks()
    {
        return $this->hasMany(UptimeCheck::class);
    }

    /**
     * Get recent checks for the specified number of days
     */
    public function recentChecks(int $days = 30)
    {
        $cutoffDate = Carbon::now('UTC')->subDays($days);

        return $this->uptimeChecks()
            ->where('checked_at', '>=', $cutoffDate)
            ->orderBy('checked_at', 'desc');
    }

    /**
     * Relationship to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the next scheduled check time
     */
    public function getNextCheckTimeAttribute(): ?Carbon
    {
        if (!$this->last_checked_at) {
            return Carbon::now('UTC');
        }

        return Carbon::parse($this->last_checked_at)
            ->utc()
            ->addSeconds($this->check_interval);
    }

    /**
     * Check if this website is due for a check
     */
    public function isDueForCheck(): bool
    {
        if (!$this->last_checked_at) {
            return true;
        }

        $now = Carbon::now('UTC');
        $lastChecked = Carbon::parse($this->last_checked_at)->utc();
        $timeDifferenceSeconds = $now->diffInSeconds($lastChecked);
        return $timeDifferenceSeconds >= $this->check_interval;
    }

    /**
     * Get minutes until next check
     */
    public function getMinutesUntilNextCheckAttribute(): int
    {
        if (!$this->last_checked_at) {
            return 0;
        }

        $now = Carbon::now('UTC');
        $nextCheck = $this->next_check_time;

        if ($nextCheck <= $now) {
            return 0;
        }

        return $now->diffInSeconds($nextCheck);
    }
}
