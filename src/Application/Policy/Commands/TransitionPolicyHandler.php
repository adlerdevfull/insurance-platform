<?php

declare(strict_types=1);

namespace Application\Policy\Commands;

use Domain\Policy\Enums\PolicyStatus;
use Domain\Policy\Repositories\PolicyRepositoryInterface;
use Domain\Policy\Entities\Policy;

final readonly class TransitionPolicyHandler
{
    public function __construct(private PolicyRepositoryInterface $policies) {}

    public function handle(int $id, string $status): Policy
    {
        $policy = $this->policies->findById($id)
            ?? throw new \DomainException("Policy not found");

        $updated = $policy->transition(PolicyStatus::from($status));

        return $this->policies->save($updated);
    }
}
