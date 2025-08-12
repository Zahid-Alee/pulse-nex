<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;


class Website extends Model
{
    /** @use HasFactory<\Database\Factories\WebsiteFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'url',
        'status',
        'check_interval',
        'last_checked_at',
        'timeout',
        'is_active'
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function uptimeChecks(): HasMany
    {
        return $this->hasMany(UptimeCheck::class);
    }

    public function recentChecks($days = 30): HasMany
    {
        return $this->uptimeChecks()
            ->where('checked_at', '>=', Carbon::now()->subDays($days))
            ->orderBy('checked_at', 'desc');
    }

    public function getUptimePercentageAttribute($days = 30)
    {
        $totalChecks = $this->recentChecks($days)->count();

        if ($totalChecks === 0) {
            return null;
        }

        $upChecks = $this->recentChecks($days)->where('status', 'up')->count();

        return round(($upChecks / $totalChecks) * 100, 2);
    }

    public function getAverageResponseTimeAttribute($days = 30)
    {
        return $this->recentChecks($days)
            ->where('status', 'up')
            ->whereNotNull('response_time')
            ->avg('response_time');
    }

    public function getCurrentDowntimeAttribute()
    {
        if ($this->status !== 'down') {
            return null;
        }

        $lastUpCheck = $this->uptimeChecks()
            ->where('status', 'up')
            ->orderBy('checked_at', 'desc')
            ->first();

        if (!$lastUpCheck) {
            return null;
        }

        return Carbon::now()->diffInMinutes($lastUpCheck->checked_at);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // public function scopeNeedsCheck($query)
    // {
    //     return $query->active()
    //         ->where(function ($q) {
    //             $q->whereNull('last_checked_at')
    //                 ->orWhereRaw('last_checked_at <= NOW() - INTERVAL check_interval SECOND');
    //         });
    // }

    public function scopeNeedsCheck($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('last_checked_at')
                ->orWhereRaw('TIMESTAMPDIFF(SECOND, last_checked_at, NOW()) >= check_interval');
        });
    }
}
