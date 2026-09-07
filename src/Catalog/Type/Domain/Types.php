<?php

declare(strict_types=1);

namespace App\Catalog\Type\Domain;

use App\Shared\Domain\Collection;

/** @extends Collection<Type> */
final class Types extends Collection
{
    protected function type(): string
    {
        return Type::class;
    }
}
