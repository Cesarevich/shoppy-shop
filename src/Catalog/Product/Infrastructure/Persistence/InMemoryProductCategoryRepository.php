<?php

declare(strict_types=1);

namespace App\Catalog\Product\Infrastructure\Persistence;

use App\Catalog\Product\Domain\ProductCategory;
use App\Catalog\Product\Domain\Repository\ProductCategoryRepository;
use App\Catalog\Product\Domain\ValueObject\ProductId;

final class InMemoryProductCategoryRepository implements ProductCategoryRepository
{
    /** @var array<string, ProductCategory> */
    private array $productCategories = [];

    public function save(ProductCategory $productCategory): void
    {
        $this->productCategories[$productCategory->productId()->value()] = $productCategory;
    }

    public function search(ProductId $id): ?ProductCategory
    {
        return $this->productCategories[$id->value()] ?? null;
    }
}
