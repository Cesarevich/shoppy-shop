<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Infrastructure\Persistence;

use App\Catalog\ProductHistory\Domain\ProductHistory;
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
}
