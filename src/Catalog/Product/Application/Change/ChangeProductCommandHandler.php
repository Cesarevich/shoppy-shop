<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Change;

use App\Catalog\Product\Domain\ProductDetails;
use App\Catalog\Product\Domain\ValueObject\Dimensions;
use App\Catalog\Product\Domain\ValueObject\Ean;
use App\Catalog\Product\Domain\ValueObject\Money;
use App\Catalog\Product\Domain\ValueObject\ProductDescription;
use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Catalog\Product\Domain\ValueObject\ProductTitle;
use App\Catalog\Product\Domain\ValueObject\Year;
use App\Shared\Domain\Bus\Command\CommandHandler;

final readonly class ChangeProductCommandHandler implements CommandHandler
{
    public function __construct(private ProductChange $changer) {}

    public function __invoke(ChangeProductCommand $command): void
    {
        $description = $command->description();
        $ean = $command->ean();
        $year = $command->year();

        $this->changer->__invoke(
            new ProductId($command->id()),
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
