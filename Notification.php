<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
        'extra_data'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'extra_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new notification
     */
    public static function createNotification(array $data)
    {
        return self::create([
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'extra_data' => $data['extra_data'] ?? null
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Get notification types
     */
    public static function getTypes()
    {
        return [
            'password_reset' => 'Password Reset',
            'kyc' => 'KYC Verification',
            'send_crypto' => 'Crypto Send',
            'receive_crypto' => 'Crypto Receive',
            'card_funding' => 'Card Funding',
            'welcome' => 'Welcome'
        ];
    }
}