<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain\Repository;

use App\Catalog\Product\Domain\ProductCategory;
use App\Catalog\Product\Domain\ValueObject\ProductId;

interface ProductCategoryRepository
{
    public function save(ProductCategory $productCategory): void;

    public function search(ProductId $id): ?ProductCategory;
}
