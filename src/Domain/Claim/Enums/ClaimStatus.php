<?php

declare(strict_types=1);

namespace Domain\Claim\Enums;

enum ClaimStatus: string
{
    case Reported     = 'reported';
    case UnderReview  = 'under_review';
    case Approved     = 'approved';
    case Paid         = 'paid';
    case Rejected     = 'rejected';

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Reported    => in_array($next, [self::UnderReview]),
            self::UnderReview => in_array($next, [self::Approved, self::Rejected]),
            self::Approved    => in_array($next, [self::Paid]),
            self::Paid,
            self::Rejected    => false,
        };
    }
}
