<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Find;

use App\Catalog\Product\Domain\Exception\ProductNotExists;
use App\Catalog\Product\Domain\Product;
use App\Catalog\Product\Domain\Repository\ProductRepository;
use App\Catalog\Product\Domain\ValueObject\ProductId;

final readonly class ProductFinder
{
    public function __construct(private ProductRepository $repository) {}

    public function __invoke(ProductId $id): Product
    {
        $product = $this->repository->search($id);
        if (null === $product) {
            throw new ProductNotExists($id);
        }

        return $product;
    }
}
