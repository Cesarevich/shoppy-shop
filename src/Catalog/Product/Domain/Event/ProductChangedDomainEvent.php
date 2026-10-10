<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain\Event;

use App\Catalog\Product\Domain\Product;
use App\Catalog\Product\Domain\ProductDetails;
use App\Catalog\Product\Domain\ValueObject\Dimensions;
use App\Shared\Domain\Bus\Event\DomainEvent;

final class ProductChangedDomainEvent extends DomainEvent
{
    /**
     * @param array<string, array{old: mixed, new: mixed}> $changes
     */
    public function __construct(
        string $id,
        private readonly array $changes,
        ?string $eventId = null,
        ?string $occurredOn = null,
    ) {
        parent::__construct($id, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'product.changed';
    }

    public static function fromChange(
        Product $product,
        ProductDetails $productDetails,
    ): ?self {
        $changes = self::changesBetween($product, $productDetails);

        if ([] === $changes) {
            return null;
        }

        return new self($product->id()->value(), $changes);
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredOn): self
    {
        return new self($aggregateId, self::changesFromBody($body), $eventId, $occurredOn);
    }

    public function toPrimitives(): array
    {
        return $this->changes;
    }

    /**
     * @return array<string, array{old: mixed, new: mixed}>
     */
    public function changes(): array
    {
        return $this->changes;
    }

    /**
     * @param array<string, mixed> $body
     *
     * @return array<string, array{old: mixed, new: mixed}>
     */
    // toDo метод выглядит сомнительно changesFromBody ->
    private static function changesFromBody(array $body): array
    {
        $changes = [];

        foreach ($body as $field => $change) {
            if (!is_array($change) || !array_key_exists('old', $change) || !array_key_exists('new', $change)) {
                continue;
            }

            $changes[$field] = [
                'old' => $change['old'],
                'new' => $change['new'],
            ];
        }

        return $changes;
    }

    /**
     * @return array<string, array{old: mixed, new: mixed}>
     */
    private static function changesBetween(
        Product $product,
        ProductDetails $productDetails,
    ): array {
        $changes = [];

        if (!$product->title()->equals($productDetails->title)) {
            $changes['title'] = ['old' => $product->title()->value(), 'new' => $productDetails->title->value()];
        }

        if ($product->ean()?->value() !== $productDetails->ean?->value()) {
            $changes['ean'] = ['old' => $product->ean()?->value(), 'new' => $productDetails->ean?->value()];
        }

        if ($product->description()?->value() !== $productDetails->description?->value()) {
            $changes['description'] = ['old' => $product->description()?->value(), 'new' => $productDetails->description?->value()];
        }

        if ($product->year()?->value() !== $productDetails->year?->value()) {
            $changes['year'] = ['old' => $product->year()?->value(), 'new' => $productDetails->year?->value()];
        }

        if (!self::sameDimensions($product->dimensions(), $productDetails->dimensions)) {
            $changes['dimensions'] = [
                'old' => self::dimensionsPayload($product->dimensions()),
                'new' => self::dimensionsPayload($productDetails->dimensions),
            ];
        }

        if (!$product->listPrice()->equals($productDetails->listPrice)) {
            $changes['listPrice'] = [
                'old' => ['amount' => $product->listPrice()->amount(), 'currency' => $product->listPrice()->currency()],
                'new' => ['amount' => $productDetails->listPrice->amount(), 'currency' => $productDetails->listPrice->currency()],
            ];
        }

        return $changes;
    }

    private static function sameDimensions(?Dimensions $current, ?Dimensions $next): bool
    {
        // toDo должно уехать в сам Dimension а тут простое сравнение null не null
        // todo скорость английской расскладки потренить
        $current = self::specifiedDimensions($current);
        $next = self::specifiedDimensions($next);

        if (null === $current || null === $next) {
            return null === $current && null === $next;
        }

        return $current->weight() === $next->weight()
            && $current->length() === $next->length()
            && $current->width() === $next->width()
            && $current->height() === $next->height();
    }

    // toDo подумать над мысль что в каталоге может ыть Новый год и там может быть много типов товаров,
    // toDo а не только с рутом напрмер book
    private static function specifiedDimensions(?Dimensions $dimensions): ?Dimensions
    {
        if (null === $dimensions || !$dimensions->isSpecified()) {
            return null;
        }

        return $dimensions;
    }

    /**
     * @return array{weight: int, length: int, width: int, height: int}|null
     */
    // todo тут не должно быть в самом дименшене сделать to__array
    private static function dimensionsPayload(?Dimensions $dimensions): ?array
    {
        $dimensions = self::specifiedDimensions($dimensions);
        if (null === $dimensions) {
            return null;
        }

        return [
            'weight' => $dimensions->weight(),
            'length' => $dimensions->length(),
            'width' => $dimensions->width(),
            'height' => $dimensions->height(),
        ];
    }
}
