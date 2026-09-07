<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\List;

use App\Shared\Domain\Bus\Query\QueryHandler;

final readonly class FindProductListQueryHandler implements QueryHandler
{
    public function __construct(private ProductListFinder $finder) {}

    public function __invoke(FindProductListQuery $query): ProductListResponse
    {
        return ProductListResponse::fromProducts($this->finder->__invoke());
    }
}
