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
        private ProductDetails $productDetails,
        private ListingStatus $listingStatus,
    ) {}

    public static function create(
        ProductId $id,
        TypeId $typeId,
        ProductDetails $productDetails,
    ): self {
        $product = new self($id, $typeId, $productDetails, ListingStatus::Draft);
        $product->record(ProductCreatedDomainEvent::fromProduct($product));

        return $product;
    }

    public function change(
        ProductDetails $productDetails,
    ): void {
        $event = ProductChangedDomainEvent::fromChange($this, $productDetails);

        $this->productDetails = $productDetails;

        if (null !== $event) {
            $this->record($event);
        }
    }

    public function listOnSale(): void
    {
        if ($this->listingStatus === ListingStatus::OnSale) {
            throw new ProductAlreadyOnSale($this->id);
        }

        if ($this->listingStatus !== ListingStatus::Draft) {
            throw new ProductNotInDraft($this->id);
        }

        $this->listingStatus = ListingStatus::OnSale;
        $this->record(ProductListedDomainEvent::fromProduct($this));
    }

    public function relistOnSale(): void
    {
        if ($this->listingStatus === ListingStatus::OnSale) {
            throw new ProductAlreadyOnSale($this->id);
        }
        if ($this->listingStatus !== ListingStatus::Withdrawn) {
            throw new ProductNotWithdrawn($this->id);
        }

        $this->listingStatus = ListingStatus::OnSale;
        $this->record(ProductRelistedDomainEvent::fromProduct($this));
    }

    public function withdraw(): void
    {
        if ($this->listingStatus === ListingStatus::Withdrawn) {
            throw new ProductAlreadyWithdrawn($this->id);
        }
        if ($this->listingStatus !== ListingStatus::OnSale) {
            throw new ProductNotOnSale($this->id);
        }
        $this->listingStatus = ListingStatus::Withdrawn;
        $this->record(ProductWithdrawnDomainEvent::fromProduct($this));
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
        return $this->productDetails->title;
    }

    public function ean(): ?Ean
    {
        return $this->productDetails->ean;
    }

    public function description(): ?ProductDescription
    {
        return $this->productDetails->description;
    }

    public function year(): ?Year
    {
        return $this->productDetails->year;
    }

    public function dimensions(): ?Dimensions
    {
        if (null === $this->productDetails->dimensions || !$this->productDetails->dimensions->isSpecified()) {
            return null;
        }

        return $this->productDetails->dimensions;
    }

    public function listingStatus(): ListingStatus
    {
        return $this->listingStatus;
    }

    public function listPrice(): Money
    {
        return $this->productDetails->listPrice;
    }
}
