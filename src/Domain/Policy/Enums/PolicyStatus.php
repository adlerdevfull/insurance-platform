<?php

declare(strict_types=1);

namespace Domain\Policy\Enums;

enum PolicyStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Suspended = 'suspended';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Draft      => in_array($next, [self::Active, self::Cancelled]),
            self::Active     => in_array($next, [self::Suspended, self::Cancelled, self::Expired]),
            self::Suspended  => in_array($next, [self::Active, self::Cancelled]),
            self::Cancelled,
            self::Expired    => false,
        };
    }
}
