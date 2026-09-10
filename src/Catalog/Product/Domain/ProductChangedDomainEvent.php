<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class ProductChangedDomainEvent extends DomainEvent
{
    public function __construct(
        string $id,
        private readonly string $typeId,
        private readonly string $title,
        private readonly ?string $ean,
        private readonly ?string $description,
        private readonly ?int $year,
        private readonly ?int $weight,
        private readonly ?int $length,
        private readonly ?int $width,
        private readonly ?int $height,
        private readonly string $listingStatus,
        private readonly int $listPriceAmount,
        private readonly string $listPriceCurrency,
        ?string $eventId = null,
        ?string $occurredOn = null,
    ) {
        parent::__construct($id, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'product.changed';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredOn): self
    {
        return new self(
            $aggregateId,
            self::stringFrom($body, 'typeId'),
            self::stringFrom($body, 'title'),
            self::nullableStringFrom($body, 'ean'),
            self::nullableStringFrom($body, 'description'),
            self::nullableIntFrom($body, 'year'),
            self::nullableIntFrom($body, 'weight'),
            self::nullableIntFrom($body, 'length'),
            self::nullableIntFrom($body, 'width'),
            self::nullableIntFrom($body, 'height'),
            self::stringFrom($body, 'listingStatus'),
            self::intFrom($body, 'listPriceAmount'),
            self::stringFrom($body, 'listPriceCurrency'),
            $eventId,
            $occurredOn,
        );
    }

    public function toPrimitives(): array
    {
        return [
            'typeId' => $this->typeId,
            'title' => $this->title,
            'ean' => $this->ean,
            'description' => $this->description,
            'year' => $this->year,
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'listingStatus' => $this->listingStatus,
            'listPriceAmount' => $this->listPriceAmount,
            'listPriceCurrency' => $this->listPriceCurrency,
        ];
    }

    public static function fromProduct(Product $product): self
    {
        $dimensions = $product->dimensions();

        return new self(
            $product->id()->value(),
            $product->typeId()->value(),
            $product->title()->value(),
            $product->ean()?->value(),
            $product->description()?->value(),
            $product->year()?->value(),
            $dimensions?->weight(),
            $dimensions?->length(),
            $dimensions?->width(),
            $dimensions?->height(),
            $product->listingStatus()->value,
            $product->listPrice()->amount(),
            $product->listPrice()->currency(),
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

    /** @param array<string, mixed> $body */
    private static function intFrom(array $body, string $key): int
    {
        $value = $body[$key] ?? 0;

        return is_int($value) ? $value : 0;
    }

    /** @param array<string, mixed> $body */
    private static function nullableIntFrom(array $body, string $key): ?int
    {
        $value = $body[$key] ?? null;

        return is_int($value) ? $value : null;
    }
}
