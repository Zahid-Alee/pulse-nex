<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;


    protected $fillable = [
        'user_id',
        'plan_name',
        'starts_at',
        'ends_at',
        'monitors_limit',
        'check_interval',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
