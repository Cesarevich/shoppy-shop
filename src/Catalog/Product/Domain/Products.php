<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use App\Shared\Domain\Collection;

/** @extends Collection<Product> */
final class Products extends Collection
{
    protected function type(): string
    {
        return Product::class;
    }
}
