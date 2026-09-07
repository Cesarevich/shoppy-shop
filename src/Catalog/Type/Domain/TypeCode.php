<?php

declare(strict_types=1);

namespace App\Catalog\Type\Domain;

use App\Shared\Domain\ValueObject\StringValueObject;
use InvalidArgumentException;

final class TypeCode extends StringValueObject
{
    public const MAX_LENGTH = 32;

    public function __construct(string $value)
    {
        $value = trim($value);
        if ('' === $value) {
            throw new InvalidArgumentException('Code cannot be empty.');
        }
        if (mb_strlen($value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf('Code cannot exceed %d characters.', self::MAX_LENGTH));
        }

        parent::__construct($value);
    }
}
