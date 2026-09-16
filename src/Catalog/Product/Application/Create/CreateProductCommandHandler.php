<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Create;

use App\Catalog\Product\Domain\Dimensions;
use App\Catalog\Product\Domain\Ean;
use App\Catalog\Product\Domain\Money;
use App\Catalog\Product\Domain\ProductDescription;
use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Product\Domain\ProductTitle;
use App\Catalog\Product\Domain\Year;
use App\Catalog\Type\Domain\TypeId;
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
