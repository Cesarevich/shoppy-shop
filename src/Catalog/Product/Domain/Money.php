<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use InvalidArgumentException;

final class Money
{
    public function __construct(
        protected int $amount,
        protected string $currency,
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative.');
        }
        if (1 !== preg_match('/^[A-Z]{3}$/', $currency)) {
            throw new InvalidArgumentException(sprintf('<%s> is not an ISO currency code.', $currency));
        }
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }
}
