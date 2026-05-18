<?php

declare(strict_types=1);

namespace Application\Claim\Commands;

use Domain\Claim\Entities\Claim;
use Domain\Claim\Repositories\ClaimRepositoryInterface;
use Domain\Policy\Repositories\PolicyRepositoryInterface;
use Domain\Policy\Enums\PolicyStatus;
use Domain\Shared\ValueObjects\Money;
use Infrastructure\Messaging\ClaimEventPublisher;

final readonly class ReportClaimHandler
{
    public function __construct(
        private ClaimRepositoryInterface $claims,
        private PolicyRepositoryInterface $policies,
        private ClaimEventPublisher $publisher,
    ) {}

    public function handle(
        int $policyId,
        int $userId,
        string $description,
        int $claimedAmountCents,
        string $occurredAt,
    ): Claim {
        $policy = $this->policies->findById($policyId)
            ?? throw new \DomainException("Policy not found");

        if ($policy->status !== PolicyStatus::Active) {
            throw new \DomainException("Claims can only be reported for active policies");
        }

        $claim = Claim::report(
            policyId: $policyId,
            userId: $userId,
            description: $description,
            claimedAmount: new Money($claimedAmountCents),
            occurredAt: new \DateTimeImmutable($occurredAt),
        );

        $saved = $this->claims->save($claim);
        $this->publisher->claimReported($saved);

        return $saved;
    }
}
