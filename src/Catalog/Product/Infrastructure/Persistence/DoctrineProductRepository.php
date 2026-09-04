<?php

declare(strict_types=1);

namespace App\Catalog\Product\Infrastructure\Persistence;

use App\Catalog\Product\Domain\Product;
use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Product\Domain\ProductRepository;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;

final class DoctrineProductRepository extends DoctrineRepository implements ProductRepository
{
    public function save(Product $product): void
    {
        $this->persist($product);
    }

    public function search(ProductId $id): ?Product
    {
        return $this->repository(Product::class)->find($id);
    }
}
