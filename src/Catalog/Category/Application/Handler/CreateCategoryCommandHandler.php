<?php

declare(strict_types=1);

namespace App\Catalog\Category\Application\Handler;

use App\Catalog\Category\Application\Command\CreateCategoryCommand;
use App\Catalog\Category\Application\Service\CategoryCreator;
use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Category\Domain\ValueObject\CategoryTitle;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Shared\Domain\Bus\Command\CommandHandler;

final readonly class CreateCategoryCommandHandler implements CommandHandler
{
    public function __construct(private CategoryCreator $creator) {}

    public function __invoke(CreateCategoryCommand $command): void
    {
        $parentId = $command->parentId();

        $this->creator->__invoke(
            new CategoryId($command->id()),
            new CategoryTitle($command->title()),
            new TypeId($command->typeId()),
            null !== $parentId ? new CategoryId($parentId) : null,
        );
    }
}
