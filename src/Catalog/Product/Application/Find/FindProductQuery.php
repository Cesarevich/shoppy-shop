<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Find;

use App\Shared\Domain\Bus\Query\Query;

final readonly class FindProductQuery implements Query
{
    public function __construct(private string $id) {}

    public function id(): string
    {
        return $this->id;
    }
}
