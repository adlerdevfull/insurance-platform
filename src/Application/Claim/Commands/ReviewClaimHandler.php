<?php

declare(strict_types=1);

namespace Application\Claim\Commands;

use Domain\Claim\Enums\ClaimStatus;
use Domain\Claim\Repositories\ClaimRepositoryInterface;
use Domain\Claim\Entities\Claim;
use Domain\Shared\ValueObjects\Money;
use Infrastructure\Messaging\ClaimEventPublisher;

final readonly class ReviewClaimHandler
{
    public function __construct(
        private ClaimRepositoryInterface $claims,
        private ClaimEventPublisher $publisher,
    ) {}

    public function handle(int $id, string $decision, ?int $approvedAmountCents = null, ?string $rejectionReason = null): Claim
    {
        $claim = $this->claims->findById($id)
            ?? throw new \DomainException("Claim not found");

        $updated = match ($decision) {
            'approve' => $claim->approve(new Money($approvedAmountCents ?? $claim->claimedAmount->amount())),
            'reject'  => $claim->reject($rejectionReason ?? 'No reason provided'),
            default   => throw new \DomainException("Invalid decision: {$decision}"),
        };

        $saved = $this->claims->save($updated);

        if ($updated->status === ClaimStatus::Approved) {
            $this->publisher->claimApproved($saved);
        }

        return $saved;
    }
}
