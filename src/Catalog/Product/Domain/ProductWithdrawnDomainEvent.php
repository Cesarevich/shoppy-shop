<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class ProductWithdrawnDomainEvent extends DomainEvent
{
    public function __construct(
        string $id,
        ?string $eventId = null,
        ?string $occurredOn = null,
    ) {
        parent::__construct($id, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'product.withdrawn';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredOn): self
    {
        return new self(
            $aggregateId,
            $eventId,
            $occurredOn,
        );
    }

    public function toPrimitives(): array
    {
        return [];
    }

    public static function fromProduct(Product $product): self
    {
        return new self(
            $product->id()->value(),
        );
    }
}
