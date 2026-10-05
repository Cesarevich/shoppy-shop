<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Domain;

use App\Catalog\Product\Domain\ProductId;

interface ProductHistoryRepository
{
    public function save(ProductHistory $history): void;

    public function search(string $id): ?ProductHistory;

    public function searchAll(ProductId $id): ProductHistories;
}
