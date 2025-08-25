<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UptimeCheck extends Model
{
    protected $fillable = [
        'website_id',
        'status_code',
        'status',
        'response_time',
        'is_up',
        'checked_at',
        'error_message',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'response_time' => 'integer',
        'is_up' => 'boolean',
    ];

    /**
     * Override the date casting to ensure timezone conversion
     */
    protected function asDateTime($value)
    {
        // First convert using parent method (this handles the database value)
        $datetime = parent::asDateTime($value);
        
        // If we have a valid datetime and an app timezone configured
        if ($datetime && config('app.timezone')) {
            // Convert from UTC (database storage) to app timezone for display
            return $datetime->setTimezone(config('app.timezone'));
        }
        
        return $datetime;
    }

    /**
     * Relationship to website
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    /**
     * Scope to get checks within a date range
     */
    public function scopeWithinDays($query, int $days = 7)
    {
        $appTimezone = config('app.timezone', 'UTC');
        
        $cutoffDate = Carbon::now($appTimezone)
            ->subDays($days)
            ->utc(); // Convert to UTC for database query

        return $query->where('checked_at', '>=', $cutoffDate);
    }

    /**
     * Scope to get only successful checks
     */
    public function scopeSuccessful($query)
    {
        return $query->where('is_up', true);
    }

    /**
     * Scope to get only failed checks
     */
    public function scopeFailed($query)
    {
        return $query->where('is_up', false);
    }
}