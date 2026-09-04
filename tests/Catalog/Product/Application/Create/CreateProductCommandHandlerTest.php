<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Application\Create;

use App\Catalog\Product\Application\Create\CreateProductCommandHandler;
use App\Catalog\Product\Application\Create\ProductCreator;
use App\Catalog\Product\Domain\Product;
use App\Catalog\Product\Domain\ProductCreatedDomainEvent;
use App\Catalog\Product\Domain\ProductRepository;
use App\Shared\Domain\Bus\Event\EventBus;
use App\Tests\Catalog\Product\Domain\ProductMother;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CreateProductCommandHandlerTest extends TestCase
{
    #[Test]
    public function it_should_create_a_valid_product(): void
    {
        $command = CreateProductCommandMother::create();
        $product = ProductMother::fromCommand($command);

        $repository = $this->createMock(ProductRepository::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(
                static fn(Product $saved): bool => $saved->id()->equals($product->id())
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

        $handler = new CreateProductCommandHandler(new ProductCreator($repository, $eventBus));
        $handler->__invoke($command);
    }
}
