<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bot_type',
        'trading_pair',
        'status',
        'min_amount',
        'max_amount',
        'min_final_profit',
        'max_final_profit',
        'duration',
        'total_profit',
        'total_subscribers',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'min_amount' => 'decimal:8',
        'max_amount' => 'decimal:8',
        'min_final_profit' => 'decimal:8',
        'max_final_profit' => 'decimal:8',
        'total_profit' => 'decimal:8',
        'last_trade_at' => 'datetime'
    ];

    public function subscriptions()
    {
        return $this->hasMany(BotSubscription::class);
    }

    public function trades()
    {
        return $this->hasMany(BotTrade::class);
    }
    
    /**
     * Predefined bot codes.
     */
    public function botCodes()
    {
        return $this->hasMany(BotCode::class);
    }

    public function hasActiveSubscribers(): bool
    {
        return $this->subscriptions()->where('status', 'active')->exists();
    }

    public function switchPair(string $newPair)
    {
        $oldPair = $this->trading_pair;
        $this->trading_pair = $newPair;
        $this->save();

        // Record the trade
        $this->trades()->create([
            'trading_pair' => $oldPair,
            'action' => 'switch_pair',
            'amount' => 0,
            'price' => 0,
            'metadata' => [
                'old_pair' => $oldPair,
                'new_pair' => $newPair
            ]
        ]);
    }
}
