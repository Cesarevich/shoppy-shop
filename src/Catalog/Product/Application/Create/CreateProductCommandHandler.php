<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Create;

use App\Catalog\Product\Domain\ProductDetails;
use App\Catalog\Product\Domain\ValueObject\Dimensions;
use App\Catalog\Product\Domain\ValueObject\Ean;
use App\Catalog\Product\Domain\ValueObject\Money;
use App\Catalog\Product\Domain\ValueObject\ProductDescription;
use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Catalog\Product\Domain\ValueObject\ProductTitle;
use App\Catalog\Product\Domain\ValueObject\Year;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Shared\Domain\Bus\Command\CommandHandler;

final readonly class CreateProductCommandHandler implements CommandHandler
{
    public function __construct(private ProductCreator $creator) {}

    public function __invoke(CreateProductCommand $command): void
    {
        $description = $command->description();
        $ean = $command->ean();
        $year = $command->year();

        $this->creator->__invoke(
            new ProductId($command->id()),
            new TypeId($command->typeId()),
            new ProductDetails(
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
                new Money($command->listPriceAmount(), $command->listPriceCurrency()),
            ),
        );
    }
}
