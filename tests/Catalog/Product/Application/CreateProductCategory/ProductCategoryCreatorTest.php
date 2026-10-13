<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Application\CreateProductCategory;

use App\Catalog\Category\Domain\Exception\CategoryNotExists;
use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Category\Infrastructure\Persistence\InMemoryCategoryRepository;
use App\Catalog\Product\Application\CreateProductCategory\ProductCategoryCreator;
use App\Catalog\Product\Domain\Event\ProductCategoryCreatedDomainEvent;
use App\Catalog\Product\Domain\Exception\ProductCategoryAlreadyExists;
use App\Catalog\Product\Domain\Exception\ProductCategoryTypeMismatch;
use App\Catalog\Product\Domain\Exception\ProductNotExists;
use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Catalog\Product\Infrastructure\Persistence\InMemoryProductCategoryRepository;
use App\Catalog\Product\Infrastructure\Persistence\InMemoryProductRepository;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Shared\Domain\Bus\Event\EventBus;
use App\Tests\Catalog\Category\Domain\CategoryMother;
use App\Tests\Catalog\Product\Domain\ProductCategoryMother;
use App\Tests\Catalog\Product\Domain\ProductMother;
use PHPUnit\Framework\TestCase;

final class ProductCategoryCreatorTest extends TestCase
{
    public function testCreatesProductCategory(): void
    {
        $productId = ProductId::random();
        $categoryId = CategoryId::random();
        $typeId = TypeId::random();
        $product = ProductMother::create(id: $productId, typeId: $typeId);
        $category = CategoryMother::create(id: $categoryId, typeId: $typeId);

        $productRepository = new InMemoryProductRepository();
        $productRepository->save($product);

        $productCategoryRepository = new InMemoryProductCategoryRepository();

        $categoryRepository = new InMemoryCategoryRepository();
        $categoryRepository->save($category);

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->once())
            ->method('publish')
            ->with($this->callback(
                static fn(ProductCategoryCreatedDomainEvent $event): bool => $event->aggregateId() === $productId->value()
                    && 'product.category.created' === $event::eventName()
                    && $categoryId->value() === $event->toPrimitives()['categoryId'],
            ));

        $creator = new ProductCategoryCreator(
            $productRepository,
            $productCategoryRepository,
            $categoryRepository,
            $eventBus,
        );

        $creator->__invoke($productId, $categoryId);
        $savedProductCategory = $productCategoryRepository->search($productId);
        self::assertNotNull($savedProductCategory);
        self::assertTrue($savedProductCategory->productId()->equals($productId));
        self::assertTrue($savedProductCategory->categoryId()->equals($categoryId));
    }

    public function testFailsWhenProductDoesNotExist(): void
    {
        $productId = ProductId::random();
        $categoryId = CategoryId::random();

        $productRepository = new InMemoryProductRepository();
        $productCategoryRepository = new InMemoryProductCategoryRepository();
        $categoryRepository = new InMemoryCategoryRepository();

        $eventBus = $this->createStub(EventBus::class);

        $creator = new ProductCategoryCreator(
            $productRepository,
            $productCategoryRepository,
            $categoryRepository,
            $eventBus,
        );

        $this->expectException(ProductNotExists::class);
        $creator->__invoke($productId, $categoryId);
    }

    public function testFailsWhenCategoryDoesNotExist(): void
    {
        $productId = ProductId::random();
        $categoryId = CategoryId::random();
        $typeId = TypeId::random();
        $product = ProductMother::create(id: $productId, typeId: $typeId);

        $productRepository = new InMemoryProductRepository();
        $productRepository->save($product);
        $productCategoryRepository = new InMemoryProductCategoryRepository();
        $categoryRepository = new InMemoryCategoryRepository();

        $eventBus = $this->createStub(EventBus::class);

        $creator = new ProductCategoryCreator(
            $productRepository,
            $productCategoryRepository,
            $categoryRepository,
            $eventBus,
        );

        $this->expectException(CategoryNotExists::class);
        $creator->__invoke($product->id(), $categoryId);
    }

    public function testFailsWhenTypesDiffer(): void
    {
        $productId = ProductId::random();
        $categoryId = CategoryId::random();
        $firstTypeId = TypeId::random();
        $secondTypeId = TypeId::random();
        $product = ProductMother::create(id: $productId, typeId: $firstTypeId);
        $category = CategoryMother::create(id: $categoryId, typeId: $secondTypeId);

        $productRepository = new InMemoryProductRepository();
        $productRepository->save($product);

        $productCategoryRepository = new InMemoryProductCategoryRepository();

        $categoryRepository = new InMemoryCategoryRepository();
        $categoryRepository->save($category);

        $eventBus = $this->createStub(EventBus::class);

        $creator = new ProductCategoryCreator(
            $productRepository,
            $productCategoryRepository,
            $categoryRepository,
            $eventBus,
        );

        $this->expectException(ProductCategoryTypeMismatch::class);
        $creator->__invoke($product->id(), $category->id());
    }

    public function testFailsWhenProductCategoryAlreadyExists(): void
    {
        $productId = ProductId::random();
        $categoryId = CategoryId::random();
        $typeId = TypeId::random();
        $product = ProductMother::create(id: $productId, typeId: $typeId);
        $category = CategoryMother::create(id: $categoryId, typeId: $typeId);
        $productCategory = ProductCategoryMother::create($productId, $categoryId);

        $productRepository = new InMemoryProductRepository();
        $productRepository->save($product);

        $productCategoryRepository = new InMemoryProductCategoryRepository();
        $productCategoryRepository->save($productCategory);

        $categoryRepository = new InMemoryCategoryRepository();
        $categoryRepository->save($category);

        $eventBus = $this->createStub(EventBus::class);

        $creator = new ProductCategoryCreator(
            $productRepository,
            $productCategoryRepository,
            $categoryRepository,
            $eventBus,
        );

        $this->expectException(ProductCategoryAlreadyExists::class);
        $creator->__invoke($product->id(), $category->id());
    }
}
