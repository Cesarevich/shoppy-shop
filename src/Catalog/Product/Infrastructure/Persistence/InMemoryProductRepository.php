<?php

declare(strict_types=1);

namespace App\Catalog\Product\Infrastructure\Persistence;

use App\Catalog\Product\Domain\Product;
use App\Catalog\Product\Domain\Products;
use App\Catalog\Product\Domain\Repository\ProductRepository;
use App\Catalog\Product\Domain\ValueObject\ProductId;

final class InMemoryProductRepository implements ProductRepository
{
    /** @var array<string, Product> */
    private array $products = [];

    public function save(Product $product): void
    {
        $this->products[$product->id()->value()] = $product;
    }

    public function search(ProductId $id): ?Product
    {
        return $this->products[$id->value()] ?? null;
    }

    public function searchAll(): Products
    {
        return new Products(array_values($this->products));
    }
}
