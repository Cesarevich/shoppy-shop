<?php

declare(strict_types=1);

namespace App\Catalog\Category\Domain;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class CategoryCreatedDomainEvent extends DomainEvent
{
    public function __construct(
        string $id,
        private readonly string $title,
        private readonly string $typeId,
        private readonly ?string $parentId,
        ?string $eventId = null,
        ?string $occurredOn = null,
    ) {
        parent::__construct($id, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'category.created';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredOn): self
    {
        return new self(
            $aggregateId,
            self::stringFrom($body, 'title'),
            self::stringFrom($body, 'typeId'),
            self::nullableStringFrom($body, 'parentId'),
            $eventId,
            $occurredOn,
        );
    }

    public function toPrimitives(): array
    {
        return [
            'title' => $this->title,
            'typeId' => $this->typeId,
            'parentId' => $this->parentId,
        ];
    }

    public static function fromCategory(Category $category): self
    {
        return new self(
            $category->id()->value(),
            $category->title()->value(),
            $category->typeId()->value(),
            $category->parentId()?->value(),
        );
    }

    /** @param array<string, mixed> $body */
    private static function stringFrom(array $body, string $key): string
    {
        $value = $body[$key] ?? '';

        return is_string($value) ? $value : '';
    }

    /** @param array<string, mixed> $body */
    private static function nullableStringFrom(array $body, string $key): ?string
    {
        $value = $body[$key] ?? null;

        return is_string($value) ? $value : null;
    }
}
