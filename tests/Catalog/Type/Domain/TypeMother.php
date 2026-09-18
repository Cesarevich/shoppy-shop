<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Type\Domain;

use App\Catalog\Type\Application\Create\CreateTypeCommand;
use App\Catalog\Type\Domain\Type;
use App\Catalog\Type\Domain\TypeCode;
use App\Catalog\Type\Domain\TypeId;
use App\Catalog\Type\Domain\TypeTitle;

final class TypeMother
{
    public static function create(
        ?TypeId $id = null,
        ?TypeCode $code = null,
        ?TypeTitle $title = null,
    ): Type {
        $id ??= TypeId::random();

        return new Type(
            $id,
            $code ?? new TypeCode('book-' . substr($id->value(), 0, 8)),
            $title ?? new TypeTitle('Книги'),
        );
    }

    public static function fromCommand(CreateTypeCommand $command): Type
    {
        return self::create(
            new TypeId($command->id()),
            new TypeCode($command->code()),
            new TypeTitle($command->title()),
        );
    }
}
