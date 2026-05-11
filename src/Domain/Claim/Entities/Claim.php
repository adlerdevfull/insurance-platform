<?php

declare(strict_types=1);

namespace Domain\Claim\Entities;

use Domain\Claim\Enums\ClaimStatus;
use Domain\Shared\ValueObjects\Money;

class Claim
{
    private function __construct(
        public readonly ?int $id,
        public readonly int $policyId,
        public readonly int $userId,
        public readonly string $claimNumber,
        public readonly string $description,
        public readonly Money $claimedAmount,
        public readonly ?Money $approvedAmount,
        public readonly \DateTimeImmutable $occurredAt,
        public readonly ClaimStatus $status,
        public readonly ?string $rejectionReason,
    ) {}

    public static function report(
        int $policyId,
        int $userId,
        string $description,
        Money $claimedAmount,
        \DateTimeImmutable $occurredAt,
    ): self {
        return new self(
            id: null,
            policyId: $policyId,
            userId: $userId,
            claimNumber: self::generateNumber(),
            description: $description,
            claimedAmount: $claimedAmount,
            approvedAmount: null,
            occurredAt: $occurredAt,
            status: ClaimStatus::Reported,
            rejectionReason: null,
        );
    }

    public function approve(Money $approvedAmount): self
    {
        if (!$this->status->canTransitionTo(ClaimStatus::Approved)) {
            throw new \DomainException("Cannot approve claim in status {$this->status->value}");
        }

        return new self(
            $this->id, $this->policyId, $this->userId, $this->claimNumber,
            $this->description, $this->claimedAmount, $approvedAmount,
            $this->occurredAt, ClaimStatus::Approved, null,
        );
    }

    public function reject(string $reason): self
    {
        if (!$this->status->canTransitionTo(ClaimStatus::Rejected)) {
            throw new \DomainException("Cannot reject claim in status {$this->status->value}");
        }

        return new self(
            $this->id, $this->policyId, $this->userId, $this->claimNumber,
            $this->description, $this->claimedAmount, null,
            $this->occurredAt, ClaimStatus::Rejected, $reason,
        );
    }

    public function transition(ClaimStatus $next): self
    {
        if (!$this->status->canTransitionTo($next)) {
            throw new \DomainException(
                "Cannot transition claim from {$this->status->value} to {$next->value}"
            );
        }

        return new self(
            $this->id, $this->policyId, $this->userId, $this->claimNumber,
            $this->description, $this->claimedAmount, $this->approvedAmount,
            $this->occurredAt, $next, $this->rejectionReason,
        );
    }

    private static function generateNumber(): string
    {
        return 'CLM-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }
}
