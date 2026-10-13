<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Application\CreateProductCategory;

use App\Catalog\Product\Application\CreateProductCategory\CreateProductCategoryCommand;

final class CreateProductCategoryCommandMother
{
    public static function create(
        ?string $productId = null,
        ?string $categoryId = null,
    ): CreateProductCategoryCommand {
        return new CreateProductCategoryCommand(
            $productId ?? '7b2d5a31-9e4b-4c2f-8a1d-6e3f5b7c8d9e',
            $categoryId ?? 'a1b2c3d4-e5f6-4a5b-8c9d-0e1f2a3b4c5d',
        );
    }
}
