<?php

declare(strict_types=1);

namespace App\Catalog\Type\Infrastructure\Persistence;

use App\Catalog\Type\Domain\Repository\TypeRepository;
use App\Catalog\Type\Domain\Type;
use App\Catalog\Type\Domain\ValueObject\TypeCode;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;

final class DoctrineTypeRepository extends DoctrineRepository implements TypeRepository
{
    public function save(Type $type): void
    {
        $this->persist($type);
    }

    public function search(TypeId $id): ?Type
    {
        return $this->repository(Type::class)->find($id);
    }

    public function searchByCode(TypeCode $code): ?Type
    {
        return $this->repository(Type::class)->findOneBy(['code' => $code->value()]);
    }
}
