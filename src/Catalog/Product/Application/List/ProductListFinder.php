<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\List;

use App\Catalog\Product\Domain\ProductRepository;
use App\Catalog\Product\Domain\Products;

final readonly class ProductListFinder
{
    public function __construct(private ProductRepository $repository) {}

    public function __invoke(): Products
    {
        return $this->repository->searchAll();
    }
}
