<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use App\Shared\Domain\Aggregate\AggregateRoot;

final class Product extends AggregateRoot
{
    public function __construct(
        private readonly ProductId $id,
        private readonly ProductType $type,
        private ProductTitle $title,
        private ?Ean $ean,
        private ?ProductDescription $description,
        private ?Year $year,
        private ?Dimensions $dimensions,
        private ListingStatus $listingStatus,
        private Money $listPrice,
    ) {}

    public static function create(
        ProductId $id,
        ProductType $type,
        ProductTitle $title,
        ?Ean $ean,
        ?ProductDescription $description,
        ?Year $year,
        ?Dimensions $dimensions,
        ListingStatus $listingStatus,
        Money $listPrice,
    ): self {
        $product = new self($id, $type, $title, $ean, $description, $year, $dimensions, $listingStatus, $listPrice);
        $product->record(ProductCreatedDomainEvent::fromProduct($product));

        return $product;
    }

    public function id(): ProductId
    {
        return $this->id;
    }

    public function type(): ProductType
    {
        return $this->type;
    }

    public function title(): ProductTitle
    {
        return $this->title;
    }

    public function ean(): ?Ean
    {
        return $this->ean;
    }

    public function description(): ?ProductDescription
    {
        return $this->description;
    }

    public function year(): ?Year
    {
        return $this->year;
    }

    public function dimensions(): ?Dimensions
    {
        if (null === $this->dimensions || !$this->dimensions->isSpecified()) {
            return null;
        }

        return $this->dimensions;
    }

    public function listingStatus(): ListingStatus
    {
        return $this->listingStatus;
    }

    public function listPrice(): Money
    {
        return $this->listPrice;
    }
}
