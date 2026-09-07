<?php

declare(strict_types=1);

namespace App\Catalog\Type\Infrastructure\Persistence;

use App\Catalog\Type\Domain\Type;
use App\Catalog\Type\Domain\TypeId;
use App\Catalog\Type\Domain\TypeRepository;

final class InMemoryTypeRepository implements TypeRepository
{
    /** @var array<string, Type> */
    private array $types = [];

    public function save(Type $type): void
    {
        $this->types[$type->id()->value()] = $type;
    }

    public function search(TypeId $id): ?Type
    {
        return $this->types[$id->value()] ?? null;
    }
}
