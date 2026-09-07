<?php

declare(strict_types=1);

namespace App\Catalog\Type\Domain;

use App\Shared\Domain\Aggregate\AggregateRoot;

final class Type extends AggregateRoot
{
    public function __construct(
        private readonly TypeId $id,
        private readonly TypeCode $code,
        private TypeTitle $title,
    ) {}

    public static function create(
        TypeId $id,
        TypeCode $code,
        TypeTitle $title,
    ): self {
        $type = new self($id, $code, $title);
        $type->record(TypeCreatedDomainEvent::fromType($type));

        return $type;
    }

    public function id(): TypeId
    {
        return $this->id;
    }

    public function code(): TypeCode
    {
        return $this->code;
    }

    public function title(): TypeTitle
    {
        return $this->title;
    }
}
