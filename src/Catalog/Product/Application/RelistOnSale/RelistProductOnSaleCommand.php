<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\RelistOnSale;

use App\Shared\Domain\Bus\Command\Command;

final readonly class RelistProductOnSaleCommand implements Command
{
    public function __construct(
        private string $id,
    ) {}

    public function id(): string
    {
        return $this->id;
    }
}
