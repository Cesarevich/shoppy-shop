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
    /** Name only what the test cares about; everything else gets a valid default. */
    public static function create(
        ?TypeId $id = null,
        ?TypeCode $code = null,
        ?TypeTitle $title = null,
    ): Type {
        $id ??= TypeId::random();

        return new Type(
            $id,
            // Code is unique in DB; derive it from the id so persisted fixtures never collide.
            $code ?? new TypeCode('book-' . substr($id->value(), 0, 8)),
            $title ?? new TypeTitle('Книги'),
        );
    }

    /** For handler tests, where the command is the input and the aggregate must mirror it. */
    public static function fromCommand(CreateTypeCommand $command): Type
    {
        return self::create(
            new TypeId($command->id()),
            new TypeCode($command->code()),
            new TypeTitle($command->title()),
        );
    }
}
