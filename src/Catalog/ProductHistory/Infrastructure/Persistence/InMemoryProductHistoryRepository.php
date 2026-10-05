<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Infrastructure\Persistence;

use App\Catalog\Product\Domain\ProductId;
use App\Catalog\ProductHistory\Domain\ProductHistory;
use App\Catalog\ProductHistory\Domain\ProductHistories;
use App\Catalog\ProductHistory\Domain\ProductHistoryRepository;

final class InMemoryProductHistoryRepository implements ProductHistoryRepository
{
    /** @var array<string, ProductHistory> */
    private array $entries = [];

    public function save(ProductHistory $history): void
    {
        $this->entries[$history->id()] = $history;
    }

    public function search(string $id): ?ProductHistory
    {
        return $this->entries[$id] ?? null;
    }

    public function searchAll(ProductId $id): ProductHistories
    {
        $entries = [];
        foreach ($this->entries as $history) {
            if ($history->productId()->equals($id)) {
                $entries[] = $history;
            }
        }

        usort(
            $entries,
            static fn(ProductHistory $left, ProductHistory $right): int => $left->occurredOn() <=> $right->occurredOn(),
        );

        return new ProductHistories($entries);
    }
}
