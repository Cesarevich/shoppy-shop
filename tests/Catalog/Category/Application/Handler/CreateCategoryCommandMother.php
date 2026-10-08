<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Category\Application\Handler;

use App\Catalog\Category\Application\Command\CreateCategoryCommand;

final class CreateCategoryCommandMother
{
    public static function create(
        ?string $id = null,
        string $title = 'Художественная литература',
        ?string $typeId = null,
        ?string $parentId = null,
    ): CreateCategoryCommand {
        return new CreateCategoryCommand(
            $id ?? '6ec0bd7f-11c0-43da-975e-2a8ad9ebae0b',
            $title,
            $typeId ?? '3c8f8d2e-6b1a-4f3c-9e7d-2a4b6c8d0e17',
            $parentId,
        );
    }
}
