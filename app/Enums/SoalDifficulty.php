<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum SoalDifficulty: string
{
    case EASY = 'Easy';
    case MEDIUM = 'Medium';
    case HARD = 'Hard';

    public static function options(): array
    {
        return array_map(function (self $difficulty) {
            return [
                'value' => $difficulty->value,
                // Converts 'EASY' to 'Easy', 'MEDIUM_HARD' to 'Medium Hard'
                'label' => Str::title($difficulty->name), 
            ];
        }, self::cases());
    }
}
