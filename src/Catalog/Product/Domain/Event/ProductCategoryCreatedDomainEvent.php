<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain\Event;

use App\Catalog\Product\Domain\ProductCategory;
use App\Shared\Domain\Bus\Event\DomainEvent;

final class ProductCategoryCreatedDomainEvent extends DomainEvent
{
    public function __construct(
        string $productId,
        private readonly string $categoryId,
        ?string $eventId = null,
        ?string $occurredOn = null,
    ) {
        parent::__construct($productId, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'product.category.created';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredOn): self
    {
        return new self(
            $aggregateId,
            self::stringFrom($body, 'categoryId'),
            $eventId,
            $occurredOn,
        );
    }

    public function toPrimitives(): array
    {
        return [
            'categoryId' => $this->categoryId,
        ];
    }

    public static function fromProductCategory(ProductCategory $productCategory): self
    {
        return new self(
            $productCategory->productId()->value(),
            $productCategory->categoryId()->value(),
        );
    }

    /** @param array<string, mixed> $body */
    private static function stringFrom(array $body, string $key): string
    {
        $value = $body[$key] ?? '';

        return is_string($value) ? $value : '';
    }
}
