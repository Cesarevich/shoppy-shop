<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Withdraw;

use App\Shared\Domain\Bus\Command\Command;

final readonly class ProductWithdrawCommand implements Command
{
    public function __construct(
        private string $id,
    ) {}

    public function id(): string
    {
        return $this->id;
    }
}
