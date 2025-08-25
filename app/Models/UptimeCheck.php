<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UptimeCheck extends Model
{
    /** @use HasFactory<\Database\Factories\UptimeCheckFactory> */
    use HasFactory;

    protected $fillable = [
        'website_id',
        'status',
        'response_time',
        'status_code',
        'error_message',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function getCheckedAtAttribute($value)
    {
        return $value
            ? \Carbon\Carbon::parse($value)
            ->timezone(config('app.timezone'))
            : null;
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function scopeUp($query)
    {
        return $query->where('status', 'up');
    }

    public function scopeDown($query)
    {
        return $query->where('status', 'down');
    }

    public function scopeInLastDays($query, $days)
    {
        return $query->where('checked_at', '>=', now()->subDays($days));
    }
}
