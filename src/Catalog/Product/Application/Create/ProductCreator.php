<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Create;

use App\Catalog\Product\Domain\Dimensions;
use App\Catalog\Product\Domain\Ean;
use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\Product\Domain\Money;
use App\Catalog\Product\Domain\Product;
use App\Catalog\Product\Domain\ProductDescription;
use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Product\Domain\ProductRepository;
use App\Catalog\Product\Domain\ProductTitle;
use App\Catalog\Product\Domain\ProductType;
use App\Catalog\Product\Domain\Year;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class ProductCreator
{
    public function __construct(
        private ProductRepository $repository,
        private EventBus $bus,
    ) {}

    public function __invoke(
        ProductId $id,
        ProductType $type,
        ProductTitle $title,
        ?Ean $ean,
        ?ProductDescription $description,
        ?Year $year,
        ?Dimensions $dimensions,
        ListingStatus $listingStatus,
        Money $listPrice,
    ): void {
        $product = Product::create(
            $id,
            $type,
            $title,
            $ean,
            $description,
            $year,
            $dimensions,
            $listingStatus,
            $listPrice,
        );

        $this->repository->save($product);
        $this->bus->publish(...$product->pullDomainEvents());
    }
}
