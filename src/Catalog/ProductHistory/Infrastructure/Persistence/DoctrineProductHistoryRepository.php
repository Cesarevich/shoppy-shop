<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Infrastructure\Persistence;

use App\Catalog\Product\Domain\ProductId;
use App\Catalog\ProductHistory\Domain\ProductHistory;
use App\Catalog\ProductHistory\Domain\ProductHistories;
use App\Catalog\ProductHistory\Domain\ProductHistoryRepository;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;

final class DoctrineProductHistoryRepository extends DoctrineRepository implements ProductHistoryRepository
{
    public function save(ProductHistory $history): void
    {
        $this->persist($history);
    }

    public function search(string $id): ?ProductHistory
    {
        return $this->repository(ProductHistory::class)->find($id);
    }

    public function searchAll(ProductId $id): ProductHistories
    {
        return new ProductHistories($this->repository(ProductHistory::class)->findBy(
            ['productId' => $id],
            ['occurredOn' => 'ASC'],
        ));
    }
}
