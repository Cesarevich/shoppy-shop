<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Product\Domain\Event\ProductCategoryCreatedDomainEvent;
use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Shared\Domain\Aggregate\AggregateRoot;

final class ProductCategory extends AggregateRoot
{
    public function __construct(
        private readonly ProductId $productId,
        private readonly CategoryId $categoryId,
    ) {}

    public static function create(
        ProductId $productId,
        CategoryId $categoryId,
    ): self {
        $productCategory = new self($productId, $categoryId);
        $productCategory->record(ProductCategoryCreatedDomainEvent::fromProductCategory($productCategory));

        return $productCategory;
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function categoryId(): CategoryId
    {
        return $this->categoryId;
    }
}
