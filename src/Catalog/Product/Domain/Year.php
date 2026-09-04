<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use InvalidArgumentException;

final class Year
{
    public function __construct(private readonly int $value)
    {
        if ($value < 1000 || $value > 2100) {
            throw new InvalidArgumentException(sprintf('Year <%d> is out of range.', $value));
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
