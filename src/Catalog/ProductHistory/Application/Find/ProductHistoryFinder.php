<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Application\Find;

use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Catalog\ProductHistory\Domain\ProductHistories;
use App\Catalog\ProductHistory\Domain\ProductHistoryRepository;

final readonly class ProductHistoryFinder
{
    public function __construct(private ProductHistoryRepository $repository) {}

    public function __invoke(ProductId $id): ProductHistories
    {
        return $this->repository->searchAll($id);
    }
}
