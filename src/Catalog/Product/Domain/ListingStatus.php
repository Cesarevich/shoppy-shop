<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

enum ListingStatus: string
{
    case Draft = 'draft';
    case OnSale = 'onSale';
    case Withdrawn = 'withdrawn';
}
