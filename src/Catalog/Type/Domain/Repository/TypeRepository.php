<?php

declare(strict_types=1);

namespace App\Catalog\Type\Domain\Repository;

use App\Catalog\Type\Domain\Type;
use App\Catalog\Type\Domain\ValueObject\TypeCode;
use App\Catalog\Type\Domain\ValueObject\TypeId;

interface TypeRepository
{
    public function save(Type $type): void;

    public function search(TypeId $id): ?Type;

    public function searchByCode(TypeCode $code): ?Type;
}
