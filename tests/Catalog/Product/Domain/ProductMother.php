<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Domain;

use App\Catalog\Product\Application\Create\CreateProductCommand;
use App\Catalog\Product\Domain\Dimensions;
use App\Catalog\Product\Domain\Ean;
use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\Product\Domain\Money;
use App\Catalog\Product\Domain\Product;
use App\Catalog\Product\Domain\ProductDescription;
use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Product\Domain\ProductTitle;
use App\Catalog\Product\Domain\Year;
use App\Catalog\Type\Domain\TypeId;

final class ProductMother
{
    public static function create(
        ?ProductId $id = null,
        ?TypeId $typeId = null,
        ?ProductTitle $title = null,
        ?Ean $ean = null,
        ?ProductDescription $description = null,
        ?Year $year = null,
        ?Dimensions $dimensions = null,
        ListingStatus $listingStatus = ListingStatus::Draft,
        ?Money $listPrice = null,
    ): Product {
        return new Product(
            $id ?? ProductId::random(),
            $typeId ?? TypeId::random(),
            $title ?? new ProductTitle('Clean Architecture'),
            $ean ?? new Ean('9780134494166'),
            $description ?? new ProductDescription('A craftsman guide'),
            $year ?? new Year(2017),
            $dimensions ?? new Dimensions(500, 240, 160, 30),
            $listingStatus,
            $listPrice ?? new Money(4500, 'BYN'),
        );
    }

    public static function fromCommand(CreateProductCommand $command): Product
    {
        $description = $command->description();
        $ean = $command->ean();
        $year = $command->year();

        return new Product(
            new ProductId($command->id()),
            new TypeId($command->typeId()),
            new ProductTitle($command->title()),
            null !== $ean ? new Ean($ean) : null,
            null !== $description ? new ProductDescription($description) : null,
            null !== $year ? new Year($year) : null,
            Dimensions::fromPrimitives(
                $command->weight(),
                $command->length(),
                $command->width(),
                $command->height(),
            ),
            ListingStatus::Draft,
            new Money($command->listPriceAmount(), $command->listPriceCurrency()),
        );
    }
}
