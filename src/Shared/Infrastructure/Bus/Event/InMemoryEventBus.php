<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\DomainEventSubscriber;
use App\Shared\Domain\Bus\Event\EventBus;

final class InMemoryEventBus implements EventBus
{
    /** @var array<class-string<DomainEvent>, list<DomainEventSubscriber>> */
    private array $subscribers = [];

    /** @param iterable<DomainEventSubscriber> $subscribers */
    public function __construct(iterable $subscribers)
    {
        foreach ($subscribers as $subscriber) {
            foreach ($subscriber::subscribedTo() as $eventClass) {
                $this->subscribers[$eventClass][] = $subscriber;
            }
        }
    }

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            foreach ($this->subscribers[$event::class] ?? [] as $subscriber) {
                $subscriber->__invoke($event); // @phpstan-ignore method.notFound
            }
        }
    }
}
