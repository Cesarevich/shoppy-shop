<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use App\Shared\Domain\ValueObject\StringValueObject;
use InvalidArgumentException;

final class ProductDescription extends StringValueObject
{
    public function __construct(string $value)
    {
        $value = trim($value);
        if ('' === $value) {
            throw new InvalidArgumentException('Product description cannot be empty. Pass null instead.');
        }

        parent::__construct($value);
    }
}
