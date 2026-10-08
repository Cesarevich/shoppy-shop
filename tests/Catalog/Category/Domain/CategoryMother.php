<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Category\Domain;

use App\Catalog\Category\Application\Command\CreateCategoryCommand;
use App\Catalog\Category\Domain\Category;
use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Category\Domain\ValueObject\CategoryTitle;
use App\Catalog\Type\Domain\ValueObject\TypeId;

final class CategoryMother
{
    public static function create(
        ?CategoryId $id = null,
        ?CategoryTitle $title = null,
        ?TypeId $typeId = null,
        ?CategoryId $parentId = null,
    ): Category {
        return new Category(
            $id ?? CategoryId::random(),
            $title ?? new CategoryTitle('Художественная литература'),
            $typeId ?? TypeId::random(),
            $parentId,
        );
    }

    public static function fromCommand(CreateCategoryCommand $command): Category
    {
        $parentId = $command->parentId();

        return self::create(
            new CategoryId($command->id()),
            new CategoryTitle($command->title()),
            new TypeId($command->typeId()),
            null !== $parentId ? new CategoryId($parentId) : null,
        );
    }
}
