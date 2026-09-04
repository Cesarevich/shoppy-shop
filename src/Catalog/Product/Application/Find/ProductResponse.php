<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Find;

use App\Catalog\Product\Domain\Product;
use App\Shared\Domain\Bus\Query\Response;

final readonly class ProductResponse implements Response
{
    public function __construct(
        public string $id,
        public string $type,
        public string $title,
        public ?string $ean,
        public ?string $description,
        public ?int $year,
        public ?int $weight,
        public ?int $length,
        public ?int $width,
        public ?int $height,
        public string $listingStatus,
        public int $listPriceAmount,
        public string $listPriceCurrency,
    ) {}

    public static function fromProduct(Product $product): self
    {
        $dimensions = $product->dimensions();

        return new self(
            $product->id()->value(),
            $product->type()->value(),
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

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
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
}
