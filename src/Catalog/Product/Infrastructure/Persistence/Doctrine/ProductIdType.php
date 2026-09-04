<?php

declare(strict_types=1);

namespace App\Catalog\Product\Infrastructure\Persistence\Doctrine;

use App\Catalog\Product\Domain\ProductId;
use App\Shared\Infrastructure\Persistence\Doctrine\UuidType;

final class ProductIdType extends UuidType
{
    public const NAME = 'product_id';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function typeClassName(): string
    {
        return ProductId::class;
    }
}
