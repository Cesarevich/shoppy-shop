<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Change;

use App\Catalog\Product\Domain\Dimensions;
use App\Catalog\Product\Domain\Ean;
use App\Catalog\Product\Domain\Money;
use App\Catalog\Product\Domain\ProductDescription;
use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Product\Domain\ProductTitle;
use App\Catalog\Product\Domain\Year;
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
        );
    }
}
