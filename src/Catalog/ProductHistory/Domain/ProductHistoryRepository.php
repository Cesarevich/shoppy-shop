<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Domain;

interface ProductHistoryRepository
{
    public function save(ProductHistory $history): void;

    public function search(string $id): ?ProductHistory;
}
