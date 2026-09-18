<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Application\Create;

use App\Catalog\Product\Application\Create\CreateProductCommandHandler;
use App\Catalog\Product\Application\Create\ProductCreator;
use App\Catalog\Product\Domain\Product;
use App\Catalog\Product\Domain\ProductCreatedDomainEvent;
use App\Catalog\Product\Domain\ProductRepository;
use App\Catalog\Type\Domain\TypeId;
use App\Catalog\Type\Domain\TypeNotExist;
use App\Catalog\Type\Domain\TypeRepository;
use App\Shared\Domain\Bus\Event\EventBus;
use App\Tests\Catalog\Product\Domain\ProductMother;
use App\Tests\Catalog\Type\Domain\TypeMother;
use PHPUnit\Framework\TestCase;

final class CreateProductCommandHandlerTest extends TestCase
{
    public function testCreatesValidProduct(): void
    {
        $command = CreateProductCommandMother::create();
        $product = ProductMother::fromCommand($command);
        $type = TypeMother::create(id: new TypeId($command->typeId()));

        $types = $this->createMock(TypeRepository::class);
        $types->expects($this->once())
            ->method('search')
            ->with($this->callback(
                static fn(TypeId $typeId): bool => $typeId->equals($type->id()),
            ))
            ->willReturn($type);

        $repository = $this->createMock(ProductRepository::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(
                static fn(Product $saved): bool => $saved->id()->equals($product->id())
                    && $saved->typeId()->equals($product->typeId())
                    && $saved->title()->equals($product->title())
                    && $saved->listPrice()->equals($product->listPrice()),
            ));

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->once())
            ->method('publish')
            ->with($this->callback(
                static fn(ProductCreatedDomainEvent $event): bool => $event->aggregateId() === $product->id()->value()
                    && 'product.created' === $event::eventName(),
            ));

        $handler = new CreateProductCommandHandler(new ProductCreator($repository, $types, $eventBus));
        $handler->__invoke($command);
    }

    public function testFailsWhenTypeDoesNotExist(): void
    {
        $command = CreateProductCommandMother::create();

        $types = $this->createMock(TypeRepository::class);
        $types->expects($this->once())
            ->method('search')
            ->willReturn(null);

        $repository = $this->createMock(ProductRepository::class);
        $repository->expects($this->never())->method('save');

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->never())->method('publish');

        $handler = new CreateProductCommandHandler(new ProductCreator($repository, $types, $eventBus));

        $this->expectException(TypeNotExist::class);
        $handler->__invoke($command);
    }
}
