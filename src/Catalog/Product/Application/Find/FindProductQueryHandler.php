<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Find;

use App\Catalog\Product\Domain\ProductId;
use App\Shared\Domain\Bus\Query\QueryHandler;

final readonly class FindProductQueryHandler implements QueryHandler
{
    public function __construct(private ProductFinder $finder) {}

    public function __invoke(FindProductQuery $query): ProductResponse
    {
        $product = $this->finder->__invoke(new ProductId($query->id()));

        return ProductResponse::fromProduct($product);
    }
}
