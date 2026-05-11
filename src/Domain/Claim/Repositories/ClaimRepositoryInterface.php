<?php

declare(strict_types=1);

namespace Domain\Claim\Repositories;

use Domain\Claim\Entities\Claim;

interface ClaimRepositoryInterface
{
    public function findById(int $id): ?Claim;
    public function save(Claim $claim): Claim;
    public function paginate(int $page, int $perPage, array $filters = []): array;
    public function count(array $filters = []): int;
}
