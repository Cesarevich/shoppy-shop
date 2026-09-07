<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

interface ProductRepository
{
    public function save(Product $product): void;

    public function search(ProductId $id): ?Product;

    public function searchAll(): Products;
}
