<?php

declare(strict_types=1);

namespace App\Catalog\Type\Domain;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class TypeCreatedDomainEvent extends DomainEvent
{
    public function __construct(
        string $id,
        private readonly string $code,
        private readonly string $title,
        ?string $eventId = null,
        ?string $occurredOn = null,
    ) {
        parent::__construct($id, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'type.created';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredOn): self
    {
        return new self(
            $aggregateId,
            self::stringFrom($body, 'code'),
            self::stringFrom($body, 'title'),
            $eventId,
            $occurredOn,
        );
    }

    public function toPrimitives(): array
    {
        return [
            'code' => $this->code,
            'title' => $this->title,
        ];
    }

    public static function fromType(Type $type): self
    {
        return new self(
            $type->id()->value(),
            $type->code()->value(),
            $type->title()->value(),
        );
    }

    /** @param array<string, mixed> $body */
    private static function stringFrom(array $body, string $key): string
    {
        $value = $body[$key] ?? '';

        return is_string($value) ? $value : '';
    }
}
