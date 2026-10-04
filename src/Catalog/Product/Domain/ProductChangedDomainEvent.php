<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

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
        ProductTitle $title,
        ?Ean $ean,
        ?ProductDescription $description,
        ?Year $year,
        ?Dimensions $dimensions,
        Money $listPrice,
    ): ?self {
        $changes = self::changesBetween($product, $title, $ean, $description, $year, $dimensions, $listPrice);

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
        ProductTitle $title,
        ?Ean $ean,
        ?ProductDescription $description,
        ?Year $year,
        ?Dimensions $dimensions,
        Money $listPrice,
    ): array {
        $changes = [];

        if (!$product->title()->equals($title)) {
            $changes['title'] = ['old' => $product->title()->value(), 'new' => $title->value()];
        }

        if ($product->ean()?->value() !== $ean?->value()) {
            $changes['ean'] = ['old' => $product->ean()?->value(), 'new' => $ean?->value()];
        }

        if ($product->description()?->value() !== $description?->value()) {
            $changes['description'] = ['old' => $product->description()?->value(), 'new' => $description?->value()];
        }

        if ($product->year()?->value() !== $year?->value()) {
            $changes['year'] = ['old' => $product->year()?->value(), 'new' => $year?->value()];
        }

        if (!self::sameDimensions($product->dimensions(), $dimensions)) {
            $changes['dimensions'] = [
                'old' => self::dimensionsPayload($product->dimensions()),
                'new' => self::dimensionsPayload($dimensions),
            ];
        }

        if (!$product->listPrice()->equals($listPrice)) {
            $changes['listPrice'] = [
                'old' => ['amount' => $product->listPrice()->amount(), 'currency' => $product->listPrice()->currency()],
                'new' => ['amount' => $listPrice->amount(), 'currency' => $listPrice->currency()],
            ];
        }

        return $changes;
    }

    private static function sameDimensions(?Dimensions $current, ?Dimensions $next): bool
    {
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
