<?php

declare(strict_types=1);

namespace App\Catalog\Type\Application\Service;

use App\Catalog\Type\Domain\Exception\TypeAlreadyExists;
use App\Catalog\Type\Domain\Exception\TypeCodeAlreadyExists;
use App\Catalog\Type\Domain\Repository\TypeRepository;
use App\Catalog\Type\Domain\Type;
use App\Catalog\Type\Domain\ValueObject\TypeCode;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Catalog\Type\Domain\ValueObject\TypeTitle;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class TypeCreator
{
    public function __construct(
        private TypeRepository $repository,
        private EventBus $bus,
    ) {}

    public function __invoke(
        TypeId $id,
        TypeCode $code,
        TypeTitle $title,
    ): void {
        $type = $this->repository->search($id);

        if (null !== $type) {
            throw new TypeAlreadyExists($id);
        }

        $typeCode = $this->repository->searchByCode($code);

        if (null !== $typeCode) {
            throw new TypeCodeAlreadyExists($code);
        }

        $type = Type::create(
            $id,
            $code,
            $title,
        );

        $this->repository->save($type);
        $this->bus->publish(...$type->pullDomainEvents());
    }
}
