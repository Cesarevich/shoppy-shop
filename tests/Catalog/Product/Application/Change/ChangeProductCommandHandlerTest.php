<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Application\Change;

use App\Catalog\Product\Application\Change\ChangeProductCommandHandler;
use App\Catalog\Product\Application\Change\ProductChange;
use App\Catalog\Product\Domain\Product;
use App\Catalog\Product\Domain\ProductChangedDomainEvent;
use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Product\Domain\ProductNotExist;
use App\Catalog\Product\Domain\ProductRepository;
use App\Shared\Domain\Bus\Event\EventBus;
use App\Tests\Catalog\Product\Application\Create\CreateProductCommandMother;
use App\Tests\Catalog\Product\Domain\ProductMother;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ChangeProductCommandHandlerTest extends TestCase
{
    #[Test]
    public function it_should_change_an_existing_product(): void
    {
        $existing = ProductMother::existing(CreateProductCommandMother::create());
        $command = ChangeProductCommandMother::create(id: $existing->id()->value(), title: 'Clean Architecture revised');

        $repository = $this->createMock(ProductRepository::class);
        $repository->expects($this->once())
            ->method('search')
            ->with($this->callback(
                static fn(ProductId $id): bool => $id->equals($existing->id()),
            ))
            ->willReturn($existing);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(
                static fn(Product $saved): bool => $saved->id()->equals($existing->id())
                    && $saved->typeId()->equals($existing->typeId())
                    && $saved->title()->value() === $command->title()
                    && $saved->year()?->value() === $command->year()
                    && $saved->listingStatus() === $existing->listingStatus(),
            ));

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->once())
            ->method('publish')
            ->with($this->callback(
                static fn(ProductChangedDomainEvent $event): bool => $event->aggregateId() === $existing->id()->value()
                    && 'product.changed' === $event::eventName(),
            ));

        $handler = new ChangeProductCommandHandler(new ProductChange($repository, $eventBus));
        $handler->__invoke($command);
    }

    #[Test]
    public function it_should_fail_when_product_does_not_exist(): void
    {
        $command = ChangeProductCommandMother::create();

        $repository = $this->createMock(ProductRepository::class);
        $repository->expects($this->once())
            ->method('search')
            ->willReturn(null);
        $repository->expects($this->never())->method('save');

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->never())->method('publish');

        $handler = new ChangeProductCommandHandler(new ProductChange($repository, $eventBus));

        $this->expectException(ProductNotExist::class);
        $handler->__invoke($command);
    }
}
