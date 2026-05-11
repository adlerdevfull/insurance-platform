<?php

declare(strict_types=1);

namespace Domain\Policy\Entities;

use Domain\Policy\Enums\PolicyStatus;
use Domain\Policy\Enums\RiskType;
use Domain\Shared\ValueObjects\Money;

class Policy
{
    private function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly string $policyNumber,
        public readonly string $insuredName,
        public readonly string $insuredDocument,
        public readonly RiskType $riskType,
        public readonly Money $basePremium,
        public readonly Money $premium,
        public readonly \DateTimeImmutable $startsAt,
        public readonly \DateTimeImmutable $expiresAt,
        public PolicyStatus $status,
    ) {}

    public static function create(
        int $userId,
        string $insuredName,
        string $insuredDocument,
        RiskType $riskType,
        Money $basePremium,
        \DateTimeImmutable $startsAt,
        \DateTimeImmutable $expiresAt,
    ): self {
        if ($expiresAt <= $startsAt) {
            throw new \DomainException('Expiry date must be after start date');
        }

        $premium = $basePremium->multiply($riskType->premiumFactor());

        return new self(
            id: null,
            userId: $userId,
            policyNumber: self::generateNumber(),
            insuredName: $insuredName,
            insuredDocument: $insuredDocument,
            riskType: $riskType,
            basePremium: $basePremium,
            premium: $premium,
            startsAt: $startsAt,
            expiresAt: $expiresAt,
            status: PolicyStatus::Draft,
        );
    }

    public function transition(PolicyStatus $next): self
    {
        if (!$this->status->canTransitionTo($next)) {
            throw new \DomainException(
                "Cannot transition policy from {$this->status->value} to {$next->value}"
            );
        }

        return new self(
            $this->id, $this->userId, $this->policyNumber,
            $this->insuredName, $this->insuredDocument,
            $this->riskType, $this->basePremium, $this->premium,
            $this->startsAt, $this->expiresAt, $next,
        );
    }

    private static function generateNumber(): string
    {
        return 'POL-' . strtoupper(substr(md5(uniqid()), 0, 8));
    }
}
