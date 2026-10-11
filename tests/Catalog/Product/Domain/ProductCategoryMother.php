<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Domain;

use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Product\Application\CreateProductCategory\CreateProductCategoryCommand;
use App\Catalog\Product\Domain\ProductCategory;
use App\Catalog\Product\Domain\ValueObject\ProductId;

final class ProductCategoryMother
{
    public static function create(
        ?ProductId $productId = null,
        ?CategoryId $categoryId = null,
    ): ProductCategory {
        return new ProductCategory(
            $productId ?? ProductId::random(),
            $categoryId ?? CategoryId::random(),
        );
    }

    public static function fromCommand(CreateProductCategoryCommand $command): ProductCategory
    {
        return new ProductCategory(
            new ProductId($command->productId()),
            new CategoryId($command->categoryId()),
        );
    }
}
