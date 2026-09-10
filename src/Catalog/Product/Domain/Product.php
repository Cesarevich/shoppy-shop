<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use App\Catalog\Type\Domain\TypeId;
use App\Shared\Domain\Aggregate\AggregateRoot;

final class Product extends AggregateRoot
{
    public function __construct(
        private readonly ProductId $id,
        private readonly TypeId $typeId,
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
        TypeId $typeId,
        ProductTitle $title,
        ?Ean $ean,
        ?ProductDescription $description,
        ?Year $year,
        ?Dimensions $dimensions,
        ListingStatus $listingStatus,
        Money $listPrice,
    ): self {
        $product = new self($id, $typeId, $title, $ean, $description, $year, $dimensions, $listingStatus, $listPrice);
        $product->record(ProductCreatedDomainEvent::fromProduct($product));

        return $product;
    }

    public function change(
        ProductTitle $title,
        ?Ean $ean,
        ?ProductDescription $description,
        ?Year $year,
        ?Dimensions $dimensions,
        Money $listPrice,
    ): void {
        $this->title = $title;
        $this->ean = $ean;
        $this->description = $description;
        $this->year = $year;
        $this->dimensions = $dimensions;
        $this->listPrice = $listPrice;
        $this->record(ProductChangedDomainEvent::fromProduct($this));
    }

    public function id(): ProductId
    {
        return $this->id;
    }

    public function typeId(): TypeId
    {
        return $this->typeId;
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
