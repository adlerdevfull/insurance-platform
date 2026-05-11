<?php

declare(strict_types=1);

namespace Domain\Policy\Enums;

enum RiskType: string
{
    case Low    = 'low';
    case Medium = 'medium';
    case High   = 'high';

    public function premiumFactor(): float
    {
        return match ($this) {
            self::Low    => 1.0,
            self::Medium => 1.5,
            self::High   => 2.2,
        };
    }
}
