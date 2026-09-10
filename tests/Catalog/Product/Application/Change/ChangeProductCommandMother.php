<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Application\Change;

use App\Catalog\Product\Application\Change\ChangeProductCommand;

final class ChangeProductCommandMother
{
    public static function create(
        ?string $id = null,
        string $title = 'Clean Architecture',
        ?string $ean = '9780134494166',
        ?string $description = 'A craftsman guide',
        ?int $year = 2011,
        ?int $weight = 500,
        ?int $length = 240,
        ?int $width = 160,
        ?int $height = 30,
        int $listPriceAmount = 4500,
        string $listPriceCurrency = 'BYN',
    ): ChangeProductCommand {
        return new ChangeProductCommand(
            $id ?? '1c8f8d2e-6b1a-4f3c-9e7d-2a4b6c8d0e1f',
            $title,
            $ean,
            $description,
            $year,
            $weight,
            $length,
            $width,
            $height,
            $listPriceAmount,
            $listPriceCurrency,
        );
    }
}
