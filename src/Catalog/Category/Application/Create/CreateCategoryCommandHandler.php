<?php

declare(strict_types=1);

namespace App\Catalog\Category\Application\Create;

use App\Catalog\Category\Domain\Category;
use App\Catalog\Category\Domain\Exception\CategoryAlreadyExists;
use App\Catalog\Category\Domain\Exception\CategoryNotExists;
use App\Catalog\Category\Domain\Exception\CategoryParentTypeMismatch;
use App\Catalog\Category\Domain\Repository\CategoryRepository;
use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Category\Domain\ValueObject\CategoryTitle;
use App\Catalog\Type\Domain\Exception\TypeNotExists;
use App\Catalog\Type\Domain\Repository\TypeRepository;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class CreateCategoryCommandHandler implements CommandHandler
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private TypeRepository $typeRepository,
        private EventBus $bus,
    ) {}

    public function __invoke(CreateCategoryCommand $command): void
    {
        $parentId = $command->parentId();

        $categoryId = new CategoryId($command->id());
        $categoryTitle = new CategoryTitle($command->title());
        $typeId = new TypeId($command->typeId());
        $parentId = (null !== $parentId ? new CategoryId($parentId) : null);

        if (null === $this->typeRepository->search($typeId)) {
            throw new TypeNotExists($typeId);
        }

        if (null !== $this->categoryRepository->search($categoryId)) {
            throw new CategoryAlreadyExists($categoryId);
        }

        if (null !== $parentId) {
            $parent = $this->categoryRepository->search($parentId);
            if (null === $parent) {
                throw new CategoryNotExists($parentId);
            }
            if (!$parent->typeId()->equals($typeId)) {
                throw new CategoryParentTypeMismatch($parentId, $typeId);
            }
        }

        $category = Category::create(
            $categoryId,
            $categoryTitle,
            $typeId,
            $parentId,
        );

        $this->categoryRepository->save($category);
        $this->bus->publish(...$category->pullDomainEvents());
    }
}
