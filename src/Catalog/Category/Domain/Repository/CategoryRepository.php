<?php

declare(strict_types=1);

namespace App\Catalog\Category\Domain\Repository;

use App\Catalog\Category\Domain\Category;
use App\Catalog\Category\Domain\CategoryId;

interface CategoryRepository
{
    public function save(Category $category): void;

    public function search(CategoryId $id): ?Category;
}
