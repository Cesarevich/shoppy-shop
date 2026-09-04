<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use InvalidArgumentException;

final class Ean
{
    public function __construct(private readonly string $value)
    {
        if (1 !== preg_match('/^\d{8}$|^\d{13}$/', $value)) {
            throw new InvalidArgumentException(sprintf('<%s> is not a valid EAN-8 or EAN-13.', $value));
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
