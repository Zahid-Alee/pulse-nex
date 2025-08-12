<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'status',
        'ip_address',
        'user_agent',
        'replied_at'
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /**
     * Scope to get unread contacts
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', 'unread');
    }

    /**
     * Scope to get read contacts
     */
    public function scopeRead(Builder $query): Builder
    {
        return $query->where('status', 'read');
    }

    /**
     * Scope to get replied contacts
     */
    public function scopeReplied(Builder $query): Builder
    {
        return $query->where('status', 'replied');
    }

    /**
     * Mark contact as read
     */
    public function markAsRead(): bool
    {
        return $this->update(['status' => 'read']);
    }

    /**
     * Mark contact as replied
     */
    public function markAsReplied(): bool
    {
        return $this->update([
            'status' => 'replied',
            'replied_at' => now()
        ]);
    }

    /**
     * Check if contact is unread
     */
    public function isUnread(): bool
    {
        return $this->status === 'unread';
    }

    /**
     * Check if contact is read
     */
    public function isRead(): bool
    {
        return $this->status === 'read';
    }

    /**
     * Check if contact is replied
     */
    public function isReplied(): bool
    {
        return $this->status === 'replied';
    }

    /**
     * Get formatted created date
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('M d, Y \a\t h:i A');
    }

    /**
     * Get short message preview
     */
    public function getMessagePreviewAttribute(): string
    {
        return strlen($this->message) > 100 
            ? substr($this->message, 0, 100) . '...' 
            : $this->message;
    }
}