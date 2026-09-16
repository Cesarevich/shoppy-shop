<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\ListOnSale;

use App\Shared\Domain\Bus\Command\Command;

final readonly class ListProductOnSaleCommand implements Command
{
    public function __construct(
        private string $id,
    ) {}

    public function id(): string
    {
        return $this->id;
    }
}
