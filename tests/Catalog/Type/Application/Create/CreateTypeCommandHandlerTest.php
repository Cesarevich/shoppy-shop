<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Type\Application\Create;

use App\Catalog\Type\Application\Create\CreateTypeCommandHandler;
use App\Catalog\Type\Application\Create\TypeCreator;
use App\Catalog\Type\Domain\Type;
use App\Catalog\Type\Domain\TypeCreatedDomainEvent;
use App\Catalog\Type\Domain\TypeRepository;
use App\Shared\Domain\Bus\Event\EventBus;
use App\Tests\Catalog\Type\Domain\TypeMother;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CreateTypeCommandHandlerTest extends TestCase
{
    #[Test]
    public function it_should_create_a_valid_type(): void
    {
        $command = CreateTypeCommandMother::create();
        $type = TypeMother::fromCommand($command);

        $repository = $this->createMock(TypeRepository::class);
        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(
                static fn(Type $saved): bool => $saved->id()->equals($type->id())
                    && $saved->code()->equals($type->code())
                    && $saved->title()->equals($type->title()),
            ));

        $eventBus = $this->createMock(EventBus::class);
        $eventBus->expects($this->once())
            ->method('publish')
            ->with($this->callback(
                static fn(TypeCreatedDomainEvent $event): bool => $event->aggregateId() === $type->id()->value()
                    && 'type.created' === $event::eventName(),
            ));

        $handler = new CreateTypeCommandHandler(new TypeCreator($repository, $eventBus));
        $handler->__invoke($command);
    }
}
