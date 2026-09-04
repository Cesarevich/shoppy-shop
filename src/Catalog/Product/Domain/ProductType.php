<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use InvalidArgumentException;

final class ProductType
{
    private const ALLOWED = ['book', 'generic'];

    public function __construct(private readonly string $value)
    {
        if (!in_array($value, self::ALLOWED, true)) {
            throw new InvalidArgumentException(
                sprintf('Product type <%s> is not allowed. Use book or generic.', $value),
            );
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
