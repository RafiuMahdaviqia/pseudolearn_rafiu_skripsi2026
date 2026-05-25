<?php

namespace App\Enums;

enum ClusterLabel: string
{
    case IDEAL              = 'Ideal';
    case NORMAL             = 'Normal';
    case STRUGGLING         = 'Struggling';
    case GAMING_THE_SYSTEM  = 'Gaming the System';

    public function index(): int
    {
        return array_search($this, self::cases(), true);
    }

    /**
     * Case-insensitive version of tryFrom().
     * Safe against any casing variation in stored labels.
     */
    public static function tryFromCaseInsensitive(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        foreach (self::cases() as $case) {
            if (strcasecmp($case->value, $value) === 0) {
                return $case;
            }
        }

        return null;
    }
}
