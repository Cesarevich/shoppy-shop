<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Application\Create;

use App\Catalog\Product\Application\Create\CreateProductCommand;

final class CreateProductCommandMother
{
    public static function create(
        ?string $id = null,
        string $typeId = '2c8f8d2e-6b1a-4f3c-9e7d-2a4b6c8d0e17',
        string $title = 'Clean Architecture',
        ?string $ean = '9780134494166',
        ?string $description = 'A craftsman guide',
        ?int $year = 2017,
        ?int $weight = 500,
        ?int $length = 240,
        ?int $width = 160,
        ?int $height = 30,
        string $listingStatus = 'draft',
        int $listPriceAmount = 4500,
        string $listPriceCurrency = 'BYN',
    ): CreateProductCommand {
        return new CreateProductCommand(
            $id ?? '1c8f8d2e-6b1a-4f3c-9e7d-2a4b6c8d0e1f',
            $typeId,
            $title,
            $ean,
            $description,
            $year,
            $weight,
            $length,
            $width,
            $height,
            $listingStatus,
            $listPriceAmount,
            $listPriceCurrency,
        );
    }
}
