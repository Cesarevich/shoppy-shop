<?php

declare(strict_types=1);

namespace App\Catalog\Type\Infrastructure\Persistence\Doctrine;

use App\Catalog\Type\Domain\TypeCode;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class TypeCodeType extends StringType
{
    public const NAME = 'type_code';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?TypeCode
    {
        if ($value instanceof TypeCode) {
            return $value;
        }

        $string = parent::convertToPHPValue($value, $platform);
        if (!is_string($string) || '' === $string) {
            return null;
        }

        return new TypeCode($string);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof TypeCode) {
            $value = $value->value();
        }

        $string = parent::convertToDatabaseValue($value, $platform);

        return is_string($string) ? $string : null;
    }

    /** @param array<string, mixed> $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = TypeCode::MAX_LENGTH;

        return $platform->getStringTypeDeclarationSQL($column);
    }
}
