<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Category\Application\Handler;

use App\Catalog\Category\Application\Handler\CreateCategoryCommandHandler;
use App\Catalog\Category\Application\Service\CategoryCreator;
use App\Catalog\Category\Domain\Category;
use App\Catalog\Category\Domain\Event\CategoryCreatedDomainEvent;
use App\Catalog\Category\Domain\Exception\CategoryAlreadyExists;
use App\Catalog\Category\Domain\Exception\CategoryNotExist;
use App\Catalog\Category\Domain\Exception\CategoryParentTypeMismatch;
use App\Catalog\Category\Domain\Repository\CategoryRepository;
use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Type\Domain\Exception\TypeNotExist;
use App\Catalog\Type\Domain\Repository\TypeRepository;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Shared\Domain\Bus\Event\EventBus;
use App\Tests\Catalog\Category\Domain\CategoryMother;
use App\Tests\Catalog\Type\Domain\TypeMother;
use PHPUnit\Framework\TestCase;

final class CreateCategoryCommandHandlerTest extends TestCase
{
    public function testCreatesRootCategory(): void
    {
        $command = CreateCategoryCommandMother::create();
        $category = CategoryMother::fromCommand($command);
        $type = TypeMother::create(id: new TypeId($command->typeId()));

        $repository = $this->createMock(TypeRepository::class);
        $repository->expects($this->once())
            ->method('search')
            ->with($this->callback(
                static fn(TypeId $typeId): bool => $typeId->equals($type->id()),
            ))
            ->willReturn($type);

        $categories = $this->createMock(CategoryRepository::class);
        $categories->expects($this->once())
            ->method('search')
            ->with($this->callback(
                static fn(CategoryId $id): bool => $id->equals($category->id()),
            ))
            ->willReturn(null);
        $categories->expects($this->once())
            ->method('save')
            ->with($this->callback(
                static fn(Category $saved): bool => $saved->id()->equals($category->id())
                    && $saved->title()->equals($category->title())
                    && $saved->typeId()->equals($category->typeId())
                    && null === $saved->parentId(),
            ));

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->once())
            ->method('publish')
            ->with($this->callback(
                static fn(CategoryCreatedDomainEvent $event): bool => $event->aggregateId() === $category->id()->value()
                    && 'category.created' === $event::eventName(),
            ));

        $this->handler($categories, $repository, $eventBus)->__invoke($command);
    }

    public function testCreatesChildWhenParentHasTheSameType(): void
    {
        $parent = CategoryMother::create(typeId: new TypeId('3c8f8d2e-6b1a-4f3c-9e7d-2a4b6c8d0e17'));
        $command = CreateCategoryCommandMother::create(parentId: $parent->id()->value());
        $category = CategoryMother::fromCommand($command);
        $type = TypeMother::create(id: new TypeId($command->typeId()));

        $repository = $this->createStub(TypeRepository::class);
        $repository->method('search')->willReturn($type);

        $categories = $this->createMock(CategoryRepository::class);
        $categories->expects($this->exactly(2))
            ->method('search')
            ->willReturnCallback(static function (CategoryId $id) use ($category, $parent): ?Category {
                if ($id->equals($parent->id())) {
                    return $parent;
                }
                if ($id->equals($category->id())) {
                    return null;
                }

                return null;
            });
        $categories->expects($this->once())
            ->method('save')
            ->with($this->callback(
                static fn(Category $saved): bool => $saved->id()->equals($category->id())
                    && $saved->parentId()?->equals($parent->id()),
            ));

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->once())->method('publish');

        $this->handler($categories, $repository, $eventBus)->__invoke($command);
    }

    public function testFailsWhenTypeDoesNotExist(): void
    {
        $command = CreateCategoryCommandMother::create();

        $repository = $this->createMock(TypeRepository::class);
        $repository->expects($this->once())->method('search')->willReturn(null);

        $categories = $this->createMock(CategoryRepository::class);
        $categories->expects($this->never())->method('search');
        $categories->expects($this->never())->method('save');

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->never())->method('publish');

        $this->expectException(TypeNotExist::class);
        $this->handler($categories, $repository, $eventBus)->__invoke($command);
    }

    public function testFailsWhenCategoryAlreadyExists(): void
    {
        $command = CreateCategoryCommandMother::create();
        $existing = CategoryMother::fromCommand($command);
        $type = TypeMother::create(id: new TypeId($command->typeId()));

        $repository = $this->createStub(TypeRepository::class);
        $repository->method('search')->willReturn($type);

        $categories = $this->createMock(CategoryRepository::class);
        $categories->expects($this->once())->method('search')->willReturn($existing);
        $categories->expects($this->never())->method('save');

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->never())->method('publish');

        $this->expectException(CategoryAlreadyExists::class);
        $this->handler($categories, $repository, $eventBus)->__invoke($command);
    }

    public function testFailsWhenParentDoesNotExist(): void
    {
        $command = CreateCategoryCommandMother::create(parentId: '7ec0bd7f-11c0-43da-975e-2a8ad9ebae0c');
        $type = TypeMother::create(id: new TypeId($command->typeId()));

        $repository = $this->createStub(TypeRepository::class);
        $repository->method('search')->willReturn($type);

        $categories = $this->createMock(CategoryRepository::class);
        $categories->method('search')->willReturn(null);
        $categories->expects($this->never())->method('save');

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->never())->method('publish');

        $this->expectException(CategoryNotExist::class);
        $this->handler($categories, $repository, $eventBus)->__invoke($command);
    }

    public function testFailsWhenParentTypeDiffers(): void
    {
        $parent = CategoryMother::create(typeId: new TypeId('4c8f8d2e-6b1a-4f3c-9e7d-2a4b6c8d0e18'));
        $command = CreateCategoryCommandMother::create(parentId: $parent->id()->value());
        $category = CategoryMother::fromCommand($command);
        $type = TypeMother::create(id: new TypeId($command->typeId()));

        $repository = $this->createStub(TypeRepository::class);
        $repository->method('search')->willReturn($type);

        $categories = $this->createMock(CategoryRepository::class);
        $categories->method('search')->willReturnCallback(
            static function (CategoryId $id) use ($category, $parent): ?Category {
                if ($id->equals($parent->id())) {
                    return $parent;
                }
                if ($id->equals($category->id())) {
                    return null;
                }

                return null;
            },
        );
        $categories->expects($this->never())->method('save');

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->never())->method('publish');

        $this->expectException(CategoryParentTypeMismatch::class);
        $this->handler($categories, $repository, $eventBus)->__invoke($command);
    }

    private function handler(
        CategoryRepository $categories,
        TypeRepository $repository,
        EventBus $eventBus,
    ): CreateCategoryCommandHandler {
        return new CreateCategoryCommandHandler(new CategoryCreator($categories, $repository, $eventBus));
    }
}
