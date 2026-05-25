<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'claims')]
class ClaimModel
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(type: 'integer')]
    public int $policyId;

    #[ORM\Column(type: 'integer')]
    public int $userId;

    #[ORM\Column(type: 'string', unique: true)]
    public string $claimNumber;

    #[ORM\Column(type: 'text')]
    public string $description;

    #[ORM\Column(type: 'integer')]
    public int $claimedAmountCents;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $approvedAmountCents = null;

    #[ORM\Column(type: 'string')]
    public string $status;

    #[ORM\Column(type: 'string', nullable: true)]
    public ?string $rejectionReason = null;

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $occurredAt;

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $createdAt;
}
