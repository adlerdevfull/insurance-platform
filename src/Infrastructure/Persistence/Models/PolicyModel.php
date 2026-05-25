<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'policies')]
class PolicyModel
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    public ?int $id = null;

    #[ORM\Column(type: 'integer')]
    public int $userId;

    #[ORM\Column(type: 'string', unique: true)]
    public string $policyNumber;

    #[ORM\Column(type: 'string')]
    public string $insuredName;

    #[ORM\Column(type: 'string')]
    public string $insuredDocument;

    #[ORM\Column(type: 'string')]
    public string $riskType;

    #[ORM\Column(type: 'integer')]
    public int $basePremiumCents;

    #[ORM\Column(type: 'integer')]
    public int $premiumCents;

    #[ORM\Column(type: 'string')]
    public string $status;

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $startsAt;

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $expiresAt;

    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $createdAt;
}
