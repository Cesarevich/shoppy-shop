<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Domain;

use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Shared\Domain\Aggregate\AggregateRoot;

final class ProductHistory extends AggregateRoot
{
    /**
     * @param array<string, array{old: mixed, new: mixed}> $changes
     */
    public function __construct(
        private readonly string $id,
        private readonly ProductId $productId,
        private readonly string $eventName,
        private readonly string $occurredOn,
        private readonly array $changes,
    ) {}

    /**
     * @param array<string, array{old: mixed, new: mixed}> $changes
     */
    public static function create(
        string $id,
        ProductId $productId,
        string $eventName,
        string $occurredOn,
        array $changes,
    ): self {
        return new self($id, $productId, $eventName, $occurredOn, $changes);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function eventName(): string
    {
        return $this->eventName;
    }

    public function occurredOn(): string
    {
        return $this->occurredOn;
    }

    /**
     * @return array<string, array{old: mixed, new: mixed}>
     */
    public function changes(): array
    {
        return $this->changes;
    }
}
