<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain\Repository;

use App\Catalog\Product\Domain\Product;
use App\Catalog\Product\Domain\Products;
use App\Catalog\Product\Domain\ValueObject\ProductId;

interface ProductRepository
{
    public function save(Product $product): void;

    public function search(ProductId $id): ?Product;

    public function searchAll(): Products;
}
