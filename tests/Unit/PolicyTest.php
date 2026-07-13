<?php

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Policy\Entities\Policy;
use Domain\Policy\Enums\PolicyStatus;
use Domain\Policy\Enums\RiskType;
use Domain\Shared\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

class PolicyTest extends TestCase
{
    private function makePolicy(string $risk = 'low'): Policy
    {
        return Policy::create(
            userId: 1,
            insuredName: 'Carlos García',
            insuredDocument: '12345678A',
            riskType: RiskType::from($risk),
            basePremium: new Money(50000),
            startsAt: new \DateTimeImmutable('2024-01-01'),
            expiresAt: new \DateTimeImmutable('2025-01-01'),
        );
    }

    public function test_policy_created_as_draft(): void
    {
        $policy = $this->makePolicy();
        $this->assertEquals(PolicyStatus::Draft, $policy->status);
    }

    public function test_premium_calculated_by_risk(): void
    {
        $low    = $this->makePolicy('low');
        $medium = $this->makePolicy('medium');
        $high   = $this->makePolicy('high');

        $this->assertEquals(50000, $low->premium->amount());
        $this->assertEquals(75000, $medium->premium->amount());
        $this->assertEquals(110000, $high->premium->amount());
    }

    public function test_policy_transitions_draft_to_active(): void
    {
        $policy  = $this->makePolicy();
        $updated = $policy->transition(PolicyStatus::Active);
        $this->assertEquals(PolicyStatus::Active, $updated->status);
    }

    public function test_policy_cannot_transition_expired_to_active(): void
    {
        $this->expectException(\DomainException::class);
        $policy = $this->makePolicy();
        $active = $policy->transition(PolicyStatus::Active);
        $expired = $active->transition(PolicyStatus::Expired);
        $expired->transition(PolicyStatus::Active);
    }

    public function test_policy_number_generated_with_prefix(): void
    {
        $policy = $this->makePolicy();
        $this->assertStringStartsWith('POL-', $policy->policyNumber);
    }

    public function test_expiry_before_start_throws(): void
    {
        $this->expectException(\DomainException::class);
        Policy::create(
            userId: 1,
            insuredName: 'Test',
            insuredDocument: '00000000A',
            riskType: RiskType::Low,
            basePremium: new Money(10000),
            startsAt: new \DateTimeImmutable('2025-01-01'),
            expiresAt: new \DateTimeImmutable('2024-01-01'),
        );
    }
}
