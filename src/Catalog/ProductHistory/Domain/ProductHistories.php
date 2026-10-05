<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Domain;

use App\Shared\Domain\Collection;

/** @extends Collection<ProductHistory> */
final class ProductHistories extends Collection
{
    protected function type(): string
    {
        return ProductHistory::class;
    }
}
