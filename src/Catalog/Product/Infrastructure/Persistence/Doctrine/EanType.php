<?php

declare(strict_types=1);

namespace App\Catalog\Product\Infrastructure\Persistence\Doctrine;

use App\Catalog\Product\Domain\Ean;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class EanType extends StringType
{
    public const NAME = 'ean';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Ean
    {
        if ($value instanceof Ean) {
            return $value;
        }

        $string = parent::convertToPHPValue($value, $platform);
        if (!is_string($string) || '' === $string) {
            return null;
        }

        return new Ean($string);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof Ean) {
            $value = $value->value();
        }

        $string = parent::convertToDatabaseValue($value, $platform);

        return is_string($string) ? $string : null;
    }

    /** @param array<string, mixed> $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 13;

        return $platform->getStringTypeDeclarationSQL($column);
    }
}
