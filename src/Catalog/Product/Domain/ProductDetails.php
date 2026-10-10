<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use App\Catalog\Product\Domain\ValueObject\Dimensions;
use App\Catalog\Product\Domain\ValueObject\Ean;
use App\Catalog\Product\Domain\ValueObject\Money;
use App\Catalog\Product\Domain\ValueObject\ProductDescription;
use App\Catalog\Product\Domain\ValueObject\ProductTitle;
use App\Catalog\Product\Domain\ValueObject\Year;

final class ProductDetails
{
    public function __construct(
        public ProductTitle $title,
        public ?Ean $ean,
        public ?ProductDescription $description,
        public ?Year $year,
        public ?Dimensions $dimensions,
        public Money $listPrice,
    ) {}
}
