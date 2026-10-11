<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Application\Service;

use App\Catalog\Category\Domain\Exception\CategoryNotExists;
use App\Catalog\Category\Domain\Repository\CategoryRepository;
use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Product\Application\CreateProductCategory\ProductCategoryCreator;
use App\Catalog\Product\Domain\Event\ProductCategoryCreatedDomainEvent;
use App\Catalog\Product\Domain\Exception\ProductCategoryAlreadyExists;
use App\Catalog\Product\Domain\Exception\ProductCategoryTypeMismatch;
use App\Catalog\Product\Domain\Exception\ProductNotExists;
use App\Catalog\Product\Domain\ProductCategory;
use App\Catalog\Product\Domain\Repository\ProductCategoryRepository;
use App\Catalog\Product\Domain\Repository\ProductRepository;
use App\Catalog\Product\Domain\ValueObject\ProductId;
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
        $productCategory = ProductCategoryMother::create($productId, $categoryId);

        // toDo use in memoryRepo
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->once())
            ->method('search')
            ->with($this->callback(
                static fn(ProductId $id): bool => $id->equals($productId),
            ))
            ->willReturn($product);

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->expects($this->once())
            ->method('search')
            ->with($this->callback(
                static fn(CategoryId $id): bool => $id->equals($categoryId),
            ))
            ->willReturn($category);

        $productCategoryRepository = $this->createMock(ProductCategoryRepository::class);
        $productCategoryRepository->expects($this->once())
            ->method('search')
            ->with($this->callback(
                static fn(ProductId $id): bool => $id->equals($productId),
            ))
            ->willReturn(null);
        $productCategoryRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(
                static fn(ProductCategory $saved): bool => $saved->productId()->equals($productCategory->productId())
                    && $saved->categoryId()->equals($productCategory->categoryId()),
            ));

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->once())
            ->method('publish')
            ->with($this->callback(
                static fn(ProductCategoryCreatedDomainEvent $event): bool => $event->aggregateId() === $productId->value()
                    && 'product.category.created' === $event::eventName(),
            ));

        $creator = new ProductCategoryCreator(
            $productRepository,
            $productCategoryRepository,
            $categoryRepository,
            $eventBus,
        );
        $creator->__invoke($productId, $categoryId);
        // todo тут нету видимыз asserts это нормально?
    }

    public function testFailsWhenProductDoesNotExist(): void
    {
        $productId = ProductId::random();
        $categoryId = CategoryId::random();

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->once())
            ->method('search')
            ->with($this->callback(
                static fn(ProductId $id): bool => $id->equals($productId),
            ))
            ->willReturn(null);

        $categoryRepository = $this->createMock(CategoryRepository::class);
        $categoryRepository->expects($this->never())->method('search');

        $productCategoryRepository = $this->createMock(ProductCategoryRepository::class);
        $productCategoryRepository->expects($this->never())->method('search');
        $productCategoryRepository->expects($this->never())->method('save');

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->never())->method('publish');

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
        $product = ProductMother::create();
        $categoryId = CategoryId::random();
        // todo краткий вариант со стабами обсудить выше с моками
        $productRepository = $this->createStub(ProductRepository::class);
        $productRepository->method('search')->willReturn($product);

        $categoryRepository = $this->createStub(CategoryRepository::class);
        $categoryRepository->method('search')->willReturn(null);

        $productCategoryRepository = $this->createStub(ProductCategoryRepository::class);
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
        $product = ProductMother::create(typeId: TypeId::random());
        $category = CategoryMother::create(typeId: TypeId::random());

        $productRepository = $this->createStub(ProductRepository::class);
        $productRepository->method('search')->willReturn($product);

        $categoryRepository = $this->createStub(CategoryRepository::class);
        $categoryRepository->method('search')->willReturn($category);

        $productCategoryRepository = $this->createStub(ProductCategoryRepository::class);
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
        $typeId = TypeId::random();
        $product = ProductMother::create(typeId: $typeId);
        $category = CategoryMother::create(typeId: $typeId);

        $productRepository = $this->createStub(ProductRepository::class);
        $productRepository->method('search')->willReturn($product);

        $categoryRepository = $this->createStub(CategoryRepository::class);
        $categoryRepository->method('search')->willReturn($category);

        $productCategoryRepository = $this->createStub(ProductCategoryRepository::class);
        $productCategoryRepository->method('search')
            ->willReturn(ProductCategoryMother::create(productId: $product->id()));

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
