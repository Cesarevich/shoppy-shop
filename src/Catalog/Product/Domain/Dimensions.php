<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use InvalidArgumentException;

final class Dimensions
{
    public function __construct(
        protected ?int $weight,
        protected ?int $length,
        protected ?int $width,
        protected ?int $height,
    ) {
        foreach (['weight' => $weight, 'length' => $length, 'width' => $width, 'height' => $height] as $name => $value) {
            if (null !== $value && $value < 0) {
                throw new InvalidArgumentException(sprintf('Dimension <%s> cannot be negative.', $name));
            }
        }
    }

    public static function fromPrimitives(?int $weight, ?int $length, ?int $width, ?int $height): ?self
    {
        $parts = [$weight, $length, $width, $height];
        $provided = array_filter($parts, static fn(?int $v): bool => null !== $v);

        if ([] === $provided) {
            return null;
        }

        if (4 !== count($provided)) {
            throw new InvalidArgumentException('Dimensions require weight, length, width and height together.');
        }

        return new self($weight, $length, $width, $height);
    }

    public function isSpecified(): bool
    {
        return null !== $this->weight
            && null !== $this->length
            && null !== $this->width
            && null !== $this->height;
    }

    public function weight(): int
    {
        return $this->weight ?? 0;
    }

    public function length(): int
    {
        return $this->length ?? 0;
    }

    public function width(): int
    {
        return $this->width ?? 0;
    }

    public function height(): int
    {
        return $this->height ?? 0;
    }
}
