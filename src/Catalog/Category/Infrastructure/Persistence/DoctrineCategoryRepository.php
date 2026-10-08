<?php

declare(strict_types=1);

namespace App\Catalog\Category\Infrastructure\Persistence;

use App\Catalog\Category\Domain\Category;
use App\Catalog\Category\Domain\Repository\CategoryRepository;
use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;

final class DoctrineCategoryRepository extends DoctrineRepository implements CategoryRepository
{
    public function save(Category $category): void
    {
        $this->persist($category);
    }

    public function search(CategoryId $id): ?Category
    {
        return $this->repository(Category::class)->find($id);
    }
}
