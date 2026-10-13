<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Category\Application\Create;

use App\Catalog\Category\Application\Create\CreateCategoryCommandHandler;
use App\Catalog\Category\Domain\Exception\CategoryAlreadyExists;
use App\Catalog\Category\Domain\Exception\CategoryNotExists;
use App\Catalog\Category\Domain\Exception\CategoryParentTypeMismatch;
use App\Catalog\Category\Domain\Repository\CategoryRepository;
use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Category\Infrastructure\Persistence\InMemoryCategoryRepository;
use App\Catalog\Type\Domain\Exception\TypeNotExists;
use App\Catalog\Type\Domain\Repository\TypeRepository;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Catalog\Type\Infrastructure\Persistence\InMemoryTypeRepository;
use App\Shared\Domain\Bus\Event\EventBus;
use App\Tests\Catalog\Category\Domain\CategoryMother;
use App\Tests\Catalog\Type\Domain\TypeMother;
use PHPUnit\Framework\TestCase;

final class CreateCategoryCommandHandlerTest extends TestCase
{
    public function testCreatesRootCategory(): void
    {
        $command = CreateCategoryCommandMother::create();
        $type = TypeMother::create(id: new TypeId($command->typeId()));

        $typeRepository = new InMemoryTypeRepository();
        $typeRepository->save($type);

        $categoryRepository = new InMemoryCategoryRepository();
        $eventBus = $this->createStub(EventBus::class);

        $this->handler($categoryRepository, $typeRepository, $eventBus)->__invoke($command);

        $savedCategory = $categoryRepository->search(new CategoryId($command->id()));
        self::assertNotNull($savedCategory);
        self::assertSame($command->id(), $savedCategory->id()->value());
        self::assertSame($command->title(), $savedCategory->title()->value());
        self::assertSame($command->typeId(), $savedCategory->typeId()->value());
        self::assertNull($savedCategory->parentId());
    }

    public function testCreatesChildWhenParentHasTheSameType(): void
    {
        $typeId = '3c8f8d2e-6b1a-4f3c-9e7d-2a4b6c8d0e17';
        $parent = CategoryMother::create(typeId: new TypeId($typeId));
        $command = CreateCategoryCommandMother::create(
            typeId: $typeId,
            parentId: $parent->id()->value(),
        );
        $type = TypeMother::create(id: new TypeId($command->typeId()));

        $typeRepository = new InMemoryTypeRepository();
        $typeRepository->save($type);

        $categoryRepository = new InMemoryCategoryRepository();
        $categoryRepository->save($parent);
        $eventBus = $this->createStub(EventBus::class);

        $this->handler($categoryRepository, $typeRepository, $eventBus)->__invoke($command);
        $savedCategory = $categoryRepository->search(new CategoryId($command->id()));
        self::assertNotNull($savedCategory);
        self::assertSame($command->id(), $savedCategory->id()->value());
        self::assertSame($command->title(), $savedCategory->title()->value());
        self::assertSame($command->typeId(), $savedCategory->typeId()->value());
        self::assertSame($parent->id()->value(), $savedCategory->parentId()?->value());
    }

    public function testFailsWhenTypeDoesNotExist(): void
    {
        $command = CreateCategoryCommandMother::create();

        $typeRepository = new InMemoryTypeRepository();
        $categoryRepository = new InMemoryCategoryRepository();
        $eventBus = $this->createStub(EventBus::class);

        $this->expectException(TypeNotExists::class);
        $this->handler($categoryRepository, $typeRepository, $eventBus)->__invoke($command);
    }

    public function testFailsWhenCategoryAlreadyExists(): void
    {
        $command = CreateCategoryCommandMother::create();
        $type = TypeMother::create(id: new TypeId($command->typeId()));

        $typeRepository = new InMemoryTypeRepository();
        $typeRepository->save($type);

        $categoryRepository = new InMemoryCategoryRepository();
        $categoryRepository->save(CategoryMother::fromCommand($command));
        $eventBus = $this->createStub(EventBus::class);

        $this->expectException(CategoryAlreadyExists::class);
        $this->handler($categoryRepository, $typeRepository, $eventBus)->__invoke($command);
    }

    public function testFailsWhenParentDoesNotExist(): void
    {
        $command = CreateCategoryCommandMother::create(parentId: '7ec0bd7f-11c0-43da-975e-2a8ad9ebae0c');
        $type = TypeMother::create(id: new TypeId($command->typeId()));

        $typeRepository = new InMemoryTypeRepository();
        $typeRepository->save($type);

        $categoryRepository = new InMemoryCategoryRepository();
        $eventBus = $this->createStub(EventBus::class);

        $this->expectException(CategoryNotExists::class);
        $this->handler($categoryRepository, $typeRepository, $eventBus)->__invoke($command);
    }

    public function testFailsWhenParentTypeDiffers(): void
    {
        $parent = CategoryMother::create();
        $command = CreateCategoryCommandMother::create(
            typeId: '3c8f8d2e-6b1a-4f3c-9e7d-2a4b6c8d0e17',
            parentId: $parent->id()->value(),
        );
        $type = TypeMother::create(id: new TypeId($command->typeId()));

        $typeRepository = new InMemoryTypeRepository();
        $typeRepository->save($type);

        $categoryRepository = new InMemoryCategoryRepository();
        $categoryRepository->save($parent);
        $eventBus = $this->createStub(EventBus::class);

        $this->expectException(CategoryParentTypeMismatch::class);
        $this->handler($categoryRepository, $typeRepository, $eventBus)->__invoke($command);
    }

    private function handler(
        CategoryRepository $categories,
        TypeRepository $repository,
        EventBus $eventBus,
    ): CreateCategoryCommandHandler {
        return new CreateCategoryCommandHandler($categories, $repository, $eventBus);
    }
}
