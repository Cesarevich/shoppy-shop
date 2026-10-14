<?php

declare(strict_types=1);

namespace App\Tests\Unit\Catalog\Type\Application\Create;

use App\Catalog\Type\Application\Create\CreateTypeCommandHandler;
use App\Catalog\Type\Application\Create\TypeCreator;
use App\Catalog\Type\Domain\Event\TypeCreatedDomainEvent;
use App\Catalog\Type\Domain\Repository\TypeRepository;
use App\Catalog\Type\Domain\Type;
use App\Shared\Domain\Bus\Event\EventBus;
use App\Tests\Mother\Catalog\Type\TypeMother;
use App\Tests\Mother\Catalog\Type\CreateTypeCommandMother;
use PHPUnit\Framework\TestCase;

final class CreateTypeCommandHandlerTest extends TestCase
{
    public function testCreatesValidType(): void
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
