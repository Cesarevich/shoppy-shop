<?php

declare(strict_types=1);

namespace App\Catalog\Product\Infrastructure\Persistence\Doctrine;

use App\Catalog\Product\Domain\Year;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class ProductYearType extends Type
{
    public const NAME = 'product_year';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Year
    {
        if ($value instanceof Year) {
            return $value;
        }

        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return new Year((int) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof Year) {
            return $value->value();
        }

        return $value;
    }

    /** @param array<string, mixed> $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getSmallIntTypeDeclarationSQL($column);
    }
}
