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
}
