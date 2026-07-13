<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Claim\Entities\Claim;
use Domain\Claim\Enums\ClaimStatus;
use Domain\Shared\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

class ClaimTest extends TestCase
{
    private function makeClaim(): Claim
    {
        return Claim::report(
            policyId: 1,
            userId: 1,
            description: 'Accidente de tráfico',
            claimedAmount: new Money(250000),
            occurredAt: new \DateTimeImmutable('2024-02-15'),
        );
    }

    public function test_claim_created_as_reported(): void
    {
        $claim = $this->makeClaim();
        $this->assertEquals(ClaimStatus::Reported, $claim->status);
    }

    public function test_claim_number_generated_with_prefix(): void
    {
        $claim = $this->makeClaim();
        $this->assertStringStartsWith('CLM-', $claim->claimNumber);
    }

    public function test_claim_can_be_approved_from_under_review(): void
    {
        $claim      = $this->makeClaim()->transition(ClaimStatus::UnderReview);
        $approved   = $claim->approve(new Money(200000));
        $this->assertEquals(ClaimStatus::Approved, $approved->status);
        $this->assertEquals(200000, $approved->approvedAmount->amount());
    }

    public function test_claim_can_be_rejected_from_under_review(): void
    {
        $claim    = $this->makeClaim()->transition(ClaimStatus::UnderReview);
        $rejected = $claim->reject('Daños previos no declarados');
        $this->assertEquals(ClaimStatus::Rejected, $rejected->status);
        $this->assertEquals('Daños previos no declarados', $rejected->rejectionReason);
    }

    public function test_cannot_approve_reported_claim_directly(): void
    {
        $this->expectException(\DomainException::class);
        $this->makeClaim()->approve(new Money(100000));
    }

    public function test_cannot_transition_rejected_claim(): void
    {
        $this->expectException(\DomainException::class);
        $claim    = $this->makeClaim()->transition(ClaimStatus::UnderReview);
        $rejected = $claim->reject('Motivo');
        $rejected->transition(ClaimStatus::Approved);
    }
}
