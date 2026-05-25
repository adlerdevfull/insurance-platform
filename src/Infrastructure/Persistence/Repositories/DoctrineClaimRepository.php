<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Claim\Entities\Claim;
use Domain\Claim\Enums\ClaimStatus;
use Domain\Claim\Repositories\ClaimRepositoryInterface;
use Domain\Shared\ValueObjects\Money;
use Infrastructure\Persistence\Models\ClaimModel;

final class DoctrineClaimRepository implements ClaimRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function findById(int $id): ?Claim
    {
        $model = $this->em->find(ClaimModel::class, $id);
        return $model ? $this->toDomain($model) : null;
    }

    public function save(Claim $claim): Claim
    {
        $model = $claim->id ? $this->em->find(ClaimModel::class, $claim->id) : new ClaimModel();

        $model->policyId           = $claim->policyId;
        $model->userId             = $claim->userId;
        $model->claimNumber        = $claim->claimNumber;
        $model->description        = $claim->description;
        $model->claimedAmountCents = $claim->claimedAmount->amount();
        $model->approvedAmountCents = $claim->approvedAmount?->amount();
        $model->status             = $claim->status->value;
        $model->rejectionReason    = $claim->rejectionReason;
        $model->occurredAt         = $claim->occurredAt;
        $model->createdAt          = $model->createdAt ?? new \DateTimeImmutable();

        $this->em->persist($model);
        $this->em->flush();

        return $this->toDomain($model);
    }

    public function paginate(int $page, int $perPage, array $filters = []): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('c')->from(ClaimModel::class, 'c')
            ->orderBy('c.id', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        if (!empty($filters['policy_id'])) {
            $qb->andWhere('c.policyId = :policyId')->setParameter('policyId', $filters['policy_id']);
        }
        if (!empty($filters['status'])) {
            $qb->andWhere('c.status = :status')->setParameter('status', $filters['status']);
        }

        return array_map(fn($m) => $this->toDomain($m), $qb->getQuery()->getResult());
    }

    public function count(array $filters = []): int
    {
        $qb = $this->em->createQueryBuilder()
            ->select('COUNT(c.id)')->from(ClaimModel::class, 'c');

        if (!empty($filters['policy_id'])) {
            $qb->andWhere('c.policyId = :policyId')->setParameter('policyId', $filters['policy_id']);
        }
        if (!empty($filters['status'])) {
            $qb->andWhere('c.status = :status')->setParameter('status', $filters['status']);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function toDomain(ClaimModel $m): Claim
    {
        $ref   = new \ReflectionClass(Claim::class);
        $claim = $ref->newInstanceWithoutConstructor();

        $data = [
            'id'              => $m->id,
            'policyId'        => $m->policyId,
            'userId'          => $m->userId,
            'claimNumber'     => $m->claimNumber,
            'description'     => $m->description,
            'claimedAmount'   => new Money($m->claimedAmountCents),
            'approvedAmount'  => $m->approvedAmountCents !== null ? new Money($m->approvedAmountCents) : null,
            'occurredAt'      => $m->occurredAt,
            'status'          => ClaimStatus::from($m->status),
            'rejectionReason' => $m->rejectionReason,
        ];

        foreach ($data as $prop => $val) {
            $p = $ref->getProperty($prop);
            $p->setAccessible(true);
            $p->setValue($claim, $val);
        }

        return $claim;
    }
}
