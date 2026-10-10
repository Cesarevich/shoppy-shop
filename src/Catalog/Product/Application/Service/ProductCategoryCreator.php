<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Service;

use App\Catalog\Category\Domain\Exception\CategoryNotExist;
use App\Catalog\Category\Domain\Repository\CategoryRepository;
use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Product\Domain\Exception\ProductCategoryAlreadyExists;
use App\Catalog\Product\Domain\Exception\ProductCategoryTypeMismatch;
use App\Catalog\Product\Domain\Exception\ProductNotExist;
use App\Catalog\Product\Domain\ProductCategory;
use App\Catalog\Product\Domain\Repository\ProductCategoryRepository;
use App\Catalog\Product\Domain\Repository\ProductRepository;
use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class ProductCategoryCreator
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductCategoryRepository $productCategoryRepository,
        private CategoryRepository $categoryRepository,
        private EventBus $bus,
    ) {}

    public function __invoke(
        ProductId $productId,
        CategoryId $categoryId,
    ): void {
        $product = $this->productRepository->search($productId);
        if (null === $product) {
            throw new ProductNotExist($productId);
        }

        $category = $this->categoryRepository->search($categoryId);
        if (null === $category) {
            throw new CategoryNotExist($categoryId);
        }

        if (!$product->typeId()->equals($category->typeId())) {
            throw new ProductCategoryTypeMismatch(
                $productId,
                $categoryId,
                $product->typeId(),
            );
        }

        $productCategory = $this->productCategoryRepository->search($productId);
        if (null !== $productCategory) {
            throw new ProductCategoryAlreadyExists($productId, $productCategory->categoryId());
        }

        $productCategory = ProductCategory::create(
            $productId,
            $categoryId,
        );

        $this->productCategoryRepository->save($productCategory);
        $this->bus->publish(...$productCategory->pullDomainEvents());
    }
}
