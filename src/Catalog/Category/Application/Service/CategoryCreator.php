<?php

declare(strict_types=1);

namespace App\Catalog\Category\Application\Service;

use App\Catalog\Category\Domain\Category;
use App\Catalog\Category\Domain\CategoryId;
use App\Catalog\Category\Domain\CategoryTitle;
use App\Catalog\Category\Domain\Exception\CategoryAlreadyExists;
use App\Catalog\Category\Domain\Exception\CategoryNotExist;
use App\Catalog\Category\Domain\Exception\CategoryParentTypeMismatch;
use App\Catalog\Category\Domain\Repository\CategoryRepository;
use App\Catalog\Type\Domain\TypeId;
use App\Catalog\Type\Domain\TypeNotExist;
use App\Catalog\Type\Domain\TypeRepository;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class CategoryCreator
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private TypeRepository $typeRepository,
        private EventBus $bus,
    ) {}

    public function __invoke(
        CategoryId $id,
        CategoryTitle $title,
        TypeId $typeId,
        ?CategoryId $parentId,
    ): void {
        if (null === $this->typeRepository->search($typeId)) {
            throw new TypeNotExist($typeId);
        }

        if (null !== $this->categoryRepository->search($id)) {
            throw new CategoryAlreadyExists($id);
        }

        if (null !== $parentId) {
            $parent = $this->categoryRepository->search($parentId);
            if (null === $parent) {
                throw new CategoryNotExist($parentId);
            }
            if (!$parent->typeId()->equals($typeId)) {
                throw new CategoryParentTypeMismatch($parentId, $typeId);
            }
        }

        $category = Category::create(
            $id,
            $title,
            $typeId,
            $parentId,
        );

        $this->categoryRepository->save($category);
        $this->bus->publish(...$category->pullDomainEvents());
    }
}
