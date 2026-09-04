<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Event;

use App\Shared\Domain\ValueObject\SimpleUuid;
use DateTimeImmutable;
use DateTimeInterface;

abstract class DomainEvent
{
    private readonly string $eventId;
    private readonly string $occurredOn;

    public function __construct(
        private readonly string $aggregateId,
        ?string $eventId = null,
        ?string $occurredOn = null,
    ) {
        $this->eventId = $eventId ?? SimpleUuid::random()->value();
        $this->occurredOn = $occurredOn ?? (new DateTimeImmutable())->format(DateTimeInterface::ATOM);
    }

    /**
     * @param array<string, mixed> $body
     */
    abstract public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn,
    ): self;

    abstract public static function eventName(): string;

    /** @return array<string, mixed> */
    abstract public function toPrimitives(): array;

    final public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    final public function eventId(): string
    {
        return $this->eventId;
    }

    final public function occurredOn(): string
    {
        return $this->occurredOn;
    }
}
