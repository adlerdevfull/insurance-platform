<?php

declare(strict_types=1);

namespace Domain\Policy\Repositories;

use Domain\Policy\Entities\Policy;

interface PolicyRepositoryInterface
{
    public function findById(int $id): ?Policy;
    public function findByNumber(string $number): ?Policy;
    public function save(Policy $policy): Policy;
    public function paginate(int $page, int $perPage, array $filters = []): array;
    public function count(array $filters = []): int;
}
