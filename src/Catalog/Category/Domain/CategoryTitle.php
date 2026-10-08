<?php

declare(strict_types=1);

namespace App\Catalog\Category\Domain;

use App\Shared\Domain\ValueObject\StringValueObject;
use InvalidArgumentException;

final class CategoryTitle extends StringValueObject
{
    public function __construct(string $value)
    {
        $value = trim($value);
        if ('' === $value) {
            throw new InvalidArgumentException('Category title cannot be empty.');
        }
        if (mb_strlen($value) > 255) {
            throw new InvalidArgumentException('Category title cannot exceed 255 characters.');
        }

        parent::__construct($value);
    }
}
