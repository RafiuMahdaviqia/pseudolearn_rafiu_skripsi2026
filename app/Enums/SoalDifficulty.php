<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum SoalDifficulty: string
{
    case EASY = 'Easy';
    case MEDIUM = 'Medium';
    case HARD = 'Hard';

    public function index(): int
    {
        return array_search($this, self::cases(), true);
    }

    public static function fromIndex(int $index): ?self
    {
        $cases = self::cases();
        $clampedIndex = max(0, min($index, count($cases) - 1));

        return $cases[$clampedIndex];
    }

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
