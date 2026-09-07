<?php

declare(strict_types=1);

namespace App\Catalog\Type\Domain;

use App\Shared\Domain\ValueObject\StringValueObject;
use InvalidArgumentException;

final class TypeCode extends StringValueObject
{
    public function __construct(string $value)
    {
        $value = trim($value);
        if ('' === $value) {
            throw new InvalidArgumentException('Code cannot be empty.');
        }
        if (mb_strlen($value) > 255) { // toDo is 255 too many?
            throw new InvalidArgumentException('Code cannot exceed 255 characters.');
        }

        parent::__construct($value);
    }
}
