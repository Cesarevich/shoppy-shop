<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

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
