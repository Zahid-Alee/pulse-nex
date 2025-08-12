<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'plan_name',
        'amount',
        'currency',
        'payment_intent_id',
        'status',
        'monitors_limit',
        'check_interval',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
