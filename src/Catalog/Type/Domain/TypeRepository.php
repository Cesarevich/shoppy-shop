<?php

declare(strict_types=1);

namespace App\Catalog\Type\Domain;

interface TypeRepository
{
    public function save(Type $type): void;

    public function search(TypeId $id): ?Type;
}
