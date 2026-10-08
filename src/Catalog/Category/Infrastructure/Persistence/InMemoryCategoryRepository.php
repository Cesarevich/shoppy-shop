<?php

declare(strict_types=1);

namespace App\Catalog\Category\Infrastructure\Persistence;

use App\Catalog\Category\Domain\Category;
use App\Catalog\Category\Domain\Repository\CategoryRepository;
use App\Catalog\Category\Domain\ValueObject\CategoryId;

final class InMemoryCategoryRepository implements CategoryRepository
{
    /** @var array<string, Category> */
    private array $categories = [];

    public function save(Category $category): void
    {
        $this->categories[$category->id()->value()] = $category;
    }

    public function search(CategoryId $id): ?Category
    {
        return $this->categories[$id->value()] ?? null;
    }
}
