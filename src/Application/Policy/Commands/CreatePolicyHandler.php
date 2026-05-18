<?php

declare(strict_types=1);

namespace Application\Policy\Commands;

use Domain\Policy\Entities\Policy;
use Domain\Policy\Enums\RiskType;
use Domain\Policy\Repositories\PolicyRepositoryInterface;
use Domain\Shared\ValueObjects\Money;

final readonly class CreatePolicyHandler
{
    public function __construct(private PolicyRepositoryInterface $policies) {}

    public function handle(
        int $userId,
        string $insuredName,
        string $insuredDocument,
        string $riskType,
        int $basePremiumCents,
        string $startsAt,
        string $expiresAt,
    ): Policy {
        $policy = Policy::create(
            userId: $userId,
            insuredName: $insuredName,
            insuredDocument: $insuredDocument,
            riskType: RiskType::from($riskType),
            basePremium: new Money($basePremiumCents),
            startsAt: new \DateTimeImmutable($startsAt),
            expiresAt: new \DateTimeImmutable($expiresAt),
        );

        return $this->policies->save($policy);
    }
}
