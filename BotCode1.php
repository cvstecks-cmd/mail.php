<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'bot_id',
        'trading_pair',
        'duration',
        'min_final_profit',
        'max_final_profit',
        'is_active',
    ];

    protected $casts = [
        'min_final_profit' => 'decimal:8',
        'max_final_profit' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    /**
     * The bot associated with this predefined code.
     */
    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }

    /**
     * Determine whether the code is currently usable.
     */
    public function isUsable(): bool
    {
        return $this->is_active
            && $this->bot
            && $this->bot->status === 'active';
    }
}