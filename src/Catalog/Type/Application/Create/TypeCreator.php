<?php

declare(strict_types=1);

namespace App\Catalog\Type\Application\Create;

use App\Catalog\Type\Domain\Type;
use App\Catalog\Type\Domain\TypeId;
use App\Catalog\Type\Domain\TypeCode;
use App\Catalog\Type\Domain\TypeRepository;
use App\Catalog\Type\Domain\TypeTitle;
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
        $type = Type::create(
            $id,
            $code,
            $title,
        );

        $this->repository->save($type);
        $this->bus->publish(...$type->pullDomainEvents());
    }
}
