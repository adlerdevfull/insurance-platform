<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Policy\Entities\Policy;
use Domain\Policy\Enums\PolicyStatus;
use Domain\Policy\Enums\RiskType;
use Domain\Policy\Repositories\PolicyRepositoryInterface;
use Domain\Shared\ValueObjects\Money;
use Infrastructure\Persistence\Models\PolicyModel;

final class DoctrinePolicyRepository implements PolicyRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function findById(int $id): ?Policy
    {
        $model = $this->em->find(PolicyModel::class, $id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findByNumber(string $number): ?Policy
    {
        $model = $this->em->getRepository(PolicyModel::class)->findOneBy(['policyNumber' => $number]);
        return $model ? $this->toDomain($model) : null;
    }

    public function save(Policy $policy): Policy
    {
        $model = $policy->id ? $this->em->find(PolicyModel::class, $policy->id) : new PolicyModel();

        $model->userId           = $policy->userId;
        $model->policyNumber     = $policy->policyNumber;
        $model->insuredName      = $policy->insuredName;
        $model->insuredDocument  = $policy->insuredDocument;
        $model->riskType         = $policy->riskType->value;
        $model->basePremiumCents = $policy->basePremium->amount();
        $model->premiumCents     = $policy->premium->amount();
        $model->status           = $policy->status->value;
        $model->startsAt         = $policy->startsAt;
        $model->expiresAt        = $policy->expiresAt;
        $model->createdAt        = $model->createdAt ?? new \DateTimeImmutable();

        $this->em->persist($model);
        $this->em->flush();

        return $this->toDomain($model);
    }

    public function paginate(int $page, int $perPage, array $filters = []): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('p')->from(PolicyModel::class, 'p')
            ->orderBy('p.id', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        if (!empty($filters['status'])) {
            $qb->andWhere('p.status = :status')->setParameter('status', $filters['status']);
        }

        return array_map(fn($m) => $this->toDomain($m), $qb->getQuery()->getResult());
    }

    public function count(array $filters = []): int
    {
        $qb = $this->em->createQueryBuilder()
            ->select('COUNT(p.id)')->from(PolicyModel::class, 'p');

        if (!empty($filters['status'])) {
            $qb->andWhere('p.status = :status')->setParameter('status', $filters['status']);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function toDomain(PolicyModel $m): Policy
    {
        $ref    = new \ReflectionClass(Policy::class);
        $policy = $ref->newInstanceWithoutConstructor();

        $data = [
            'id'              => $m->id,
            'userId'          => $m->userId,
            'policyNumber'    => $m->policyNumber,
            'insuredName'     => $m->insuredName,
            'insuredDocument' => $m->insuredDocument,
            'riskType'        => RiskType::from($m->riskType),
            'basePremium'     => new Money($m->basePremiumCents),
            'premium'         => new Money($m->premiumCents),
            'startsAt'        => $m->startsAt,
            'expiresAt'       => $m->expiresAt,
            'status'          => PolicyStatus::from($m->status),
        ];

        foreach ($data as $prop => $val) {
            $p = $ref->getProperty($prop);
            $p->setAccessible(true);
            $p->setValue($policy, $val);
        }

        return $policy;
    }
}
