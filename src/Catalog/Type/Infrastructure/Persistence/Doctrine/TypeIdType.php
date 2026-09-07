<?php

declare(strict_types=1);

namespace App\Catalog\Type\Infrastructure\Persistence\Doctrine;

use App\Catalog\Type\Domain\TypeId;
use App\Shared\Infrastructure\Persistence\Doctrine\UuidType;

final class TypeIdType extends UuidType
{
    public const NAME = 'type_id';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function typeClassName(): string
    {
        return TypeId::class;
    }
}
