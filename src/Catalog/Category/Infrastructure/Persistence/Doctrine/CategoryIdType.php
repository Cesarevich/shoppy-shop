<?php

declare(strict_types=1);

namespace App\Catalog\Category\Infrastructure\Persistence\Doctrine;

use App\Catalog\Category\Domain\CategoryId;
use App\Shared\Infrastructure\Persistence\Doctrine\UuidType;

final class CategoryIdType extends UuidType
{
    public const NAME = 'category_id';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function typeClassName(): string
    {
        return CategoryId::class;
    }
}
