<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use App\Shared\Domain\ValueObject\StringValueObject;
use InvalidArgumentException;

final class ProductTitle extends StringValueObject
{
    public function __construct(string $value)
    {
        $value = trim($value);
        if ('' === $value) {
            throw new InvalidArgumentException('Product title cannot be empty.');
        }
        if (mb_strlen($value) > 255) {
            throw new InvalidArgumentException('Product title cannot exceed 255 characters.');
        }

        parent::__construct($value);
    }
}
