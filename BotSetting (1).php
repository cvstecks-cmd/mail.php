<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BotSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'bot_type',
        'name',
        'is_active',
        'parameters',
        'supported_pairs',
        'duration_options'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'parameters' => 'array',
        'supported_pairs' => 'array',
        'duration_options' => 'array'
    ];

    public static function getActiveBotTypes(): array
    {
        return self::where('is_active', true)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->bot_type => $setting->parameters];
            })
            ->toArray();
    }

    public static function getSupportedPairs(string $botType): array
    {
        $setting = self::where('bot_type', $botType)
            ->where('is_active', true)
            ->first();

        return $setting ? $setting->supported_pairs : [];
    }

    public static function getDurationOptions(string $botType): array
    {
        $setting = self::where('bot_type', $botType)
            ->where('is_active', true)
            ->first();

        return $setting ? $setting->duration_options : [];
    }
}