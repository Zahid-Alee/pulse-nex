<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Website extends Model
{
    protected $fillable = [
        'name',
        'url',
        'check_interval', // in seconds
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
     * Uses app timezone for consistent time handling
     */
    public function scopeNeedsCheck(Builder $query): Builder
    {
        return $query->where('check_interval', '>', 0)
            ->where(function ($q) {
                $q->whereNull('last_checked_at')
                  ->orWhereRaw('TIMESTAMPDIFF(SECOND, last_checked_at, NOW()) >= check_interval');
            })
            ->orderBy('last_checked_at', 'asc')
            ->orderBy('check_interval', 'asc');
    }

    /**
     * Alternative scope using model method
     */
    public function scopeNeedsCheckSimple(Builder $query): Builder
    {
        $websites = $query->where('check_interval', '>', 0)->get();
        $websiteIds = [];
        
        foreach ($websites as $website) {
            if ($website->isDueForCheck()) {
                $websiteIds[] = $website->id;
            }
        }
        
        return $query->whereIn('id', $websiteIds)
            ->orderBy('last_checked_at', 'asc')
            ->orderBy('check_interval', 'asc');
    }

    /**
     * Scope to get recent checks for statistics
     */
    public function scopeRecentChecks($query, int $days = 30)
    {
        $cutoffDate = Carbon::now()->subDays($days);

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
        $cutoffDate = Carbon::now()->subDays($days);

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
            return Carbon::now();
        }

        return $this->last_checked_at->copy()->addSeconds($this->check_interval);
    }

    /**
     * Check if this website is due for a check
     * Uses app timezone for consistent time handling
     */
    public function isDueForCheck(): bool
    {
        // If never checked, it's due for check
        if (!$this->last_checked_at) {
            return true;
        }

        // Use app timezone for consistent time handling
        $now = Carbon::now();
        $lastChecked = $this->last_checked_at instanceof Carbon ? 
            $this->last_checked_at : 
            Carbon::parse($this->last_checked_at);

        // Calculate seconds elapsed since last check
        $secondsElapsed = $lastChecked->diffInSeconds($now);
        
        // Check if enough time has passed
        $isDue = $secondsElapsed >= $this->check_interval;

        // Add debug logging to help troubleshoot
        \Log::debug("🔍 isDueForCheck Debug", [
            'website_id' => $this->id,
            'website_url' => $this->url,
            'now' => $now->toDateTimeString(),
            'last_checked_at' => $lastChecked->toDateTimeString(),
            'timezone' => config('app.timezone'),
            'check_interval_sec' => $this->check_interval,
            'seconds_elapsed' => $secondsElapsed,
            'is_due' => $isDue,
            'next_check_time' => $lastChecked->copy()->addSeconds($this->check_interval)->toDateTimeString()
        ]);

        return $isDue;
    }

    /**
     * Get seconds until next check
     */
    public function getSecondsUntilNextCheckAttribute(): int
    {
        if (!$this->last_checked_at) {
            return 0;
        }

        $now = Carbon::now();
        $nextCheck = $this->next_check_time;

        if ($nextCheck <= $now) {
            return 0;
        }

        return $nextCheck->diffInSeconds($now);
    }

    /**
     * Legacy method name for backwards compatibility
     */
    public function getMinutesUntilNextCheckAttribute(): int
    {
        return ceil($this->getSecondsUntilNextCheckAttribute() / 60);
    }

    /**
     * Helper method to force update last_checked_at to current time
     */
    public function touchLastCheckedAt(): void
    {
        $this->update(['last_checked_at' => Carbon::now()]);
    }
}