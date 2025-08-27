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
        'check_interval',
        'timeout',
        'status',
        'last_checked_at',
        'is_active',
        'user_id'
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'check_interval' => 'integer',
        'timeout' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * FIXED: Scope to get websites that need to be checked
     * Now properly filters by is_active status first
     */
    public function scopeNeedsCheck(Builder $query): Builder
    {
        // First filter by active websites with valid check intervals
        $websites = $query->where('is_active', true)
                          ->where('check_interval', '>', 0)
                          ->get();
        
        $websiteIds = [];

        foreach ($websites as $website) {
            if ($website->isDueForCheck()) {
                $websiteIds[] = $website->id;
            }
        }

        if (empty($websiteIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('id', $websiteIds)
            ->orderBy('last_checked_at', 'asc')
            ->orderBy('check_interval', 'asc');
    }

    /**
     * Alternative: More efficient scope using proper timezone conversion
     * This version converts database time to app timezone for comparison
     * ALSO FIXED: Now includes is_active check
     */
    public function scopeNeedsCheckEfficient(Builder $query): Builder
    {
        $appTimezone = config('app.timezone');
        $now = Carbon::now($appTimezone);

        return $query->where('is_active', true) // FIXED: Added is_active check
            ->where('check_interval', '>', 0)
            ->where(function ($q) use ($now) {
                $q->whereNull('last_checked_at')
                    ->orWhere(function ($subQuery) use ($now) {
                        // Convert the database timestamp to app timezone and compare
                        $subQuery->whereRaw(
                            'TIMESTAMPDIFF(SECOND, CONVERT_TZ(last_checked_at, @@session.time_zone, ?), ?) >= check_interval',
                            [config('app.timezone'), $now->toDateTimeString()]
                        );
                    });
            })
            ->orderBy('last_checked_at', 'asc')
            ->orderBy('check_interval', 'asc');
    }

    /**
     * Scope to get recent checks for statistics
     */
    public function scopeRecentChecks($query, int $days = 30)
    {
        $cutoffDate = Carbon::now(config('app.timezone'))->subDays($days);

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
    public function recentChecks()
    {
        return $this->uptimeChecks()
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
            return Carbon::now(config('app.timezone'));
        }

        return $this->last_checked_at->copy()
            ->setTimezone(config('app.timezone'))
            ->addSeconds($this->check_interval);
    }

    /**
     * Check if this website is due for a check - WORKING VERSION
     * This method is working correctly as shown in your logs
     */
    public function isDueForCheck(): bool
    {
        // FIXED: Check if website is active first
        if (!$this->is_active) {
            return false;
        }

        // If never checked, it's due for check
        if (!$this->last_checked_at) {
            return true;
        }

        // Use app configured timezone
        $appTimezone = config('app.timezone');
        $now = Carbon::now($appTimezone);

        // Ensure we're working with Carbon instances in the same timezone
        $lastChecked = $this->last_checked_at instanceof Carbon ?
            $this->last_checked_at->setTimezone($appTimezone) :
            Carbon::parse($this->last_checked_at)->setTimezone($appTimezone);

        // Calculate seconds elapsed since last check
        $secondsElapsed = $lastChecked->diffInSeconds($now);

        // Check if enough time has passed
        $isDue = $secondsElapsed >= $this->check_interval;

        // Add debug logging to help troubleshoot
        \Log::debug("🔍 isDueForCheck Debug", [
            'website_id' => $this->id,
            'website_url' => $this->url,
            'is_active' => $this->is_active,
            'app_timezone' => $appTimezone,
            'now' => $now->toDateTimeString(),
            'last_checked_at' => $lastChecked->toDateTimeString(),
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
        if (!$this->is_active || !$this->last_checked_at) {
            return 0;
        }

        $appTimezone = config('app.timezone');
        $now = Carbon::now($appTimezone);
        $nextCheck = $this->next_check_time->setTimezone($appTimezone);

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
     * Now uses app timezone instead of UTC
     */
    public function touchLastCheckedAt(): void
    {
        $this->update(['last_checked_at' => Carbon::now(config('app.timezone'))]);
    }
}