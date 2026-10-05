<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Application\Find;

use App\Shared\Domain\Bus\Query\QueryHandler;

final readonly class FindProductHistoryQueryHandler implements QueryHandler
{
    public function __construct(private ProductHistoryFinder $finder) {}

    public function __invoke(FindProductHistoryQuery $query): ProductHistoriesResponse
    {
        return ProductHistoriesResponse::fromProductHistories($this->finder->__invoke($query->id()));
    }
}
