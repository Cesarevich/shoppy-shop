<?php

declare(strict_types=1);

namespace App\Catalog\Product\Infrastructure\Persistence;

use App\Catalog\Product\Domain\ProductCategory;
use App\Catalog\Product\Domain\Repository\ProductCategoryRepository;
use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;

final class DoctrineProductCategoryRepository extends DoctrineRepository implements ProductCategoryRepository
{
    public function save(ProductCategory $productCategory): void
    {
        $this->persist($productCategory);
    }

    public function search(ProductId $id): ?ProductCategory
    {
        return $this->repository(ProductCategory::class)->find($id);
    }
}
