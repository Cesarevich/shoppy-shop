<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Application\Find;

use App\Catalog\Product\Domain\ProductId;
use App\Shared\Domain\Bus\Query\Query;

final readonly class FindProductHistoryQuery implements Query
{
    public function __construct(private ProductId $id) {}

    public function id(): ProductId
    {
        return $this->id;
    }
}
