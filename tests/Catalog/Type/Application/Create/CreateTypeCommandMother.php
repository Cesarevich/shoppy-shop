<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Type\Application\Create;

use App\Catalog\Type\Application\Create\CreateTypeCommand;

final class CreateTypeCommandMother
{
    public static function create(
        ?string $id = null,
        string $code = 'book',
        string $title = 'Книги',
    ): CreateTypeCommand {
        return new CreateTypeCommand(
            $id ?? '2c8f8d2e-6b1a-4f3c-9e7d-2a4b6c8d0e17',
            $code,
            $title,
        );
    }
}
