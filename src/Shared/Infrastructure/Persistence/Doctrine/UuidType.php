<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\ValueObject\Uuid;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

abstract class UuidType extends StringType
{
    /** @return class-string<Uuid> */
    abstract protected function typeClassName(): string;

    abstract public function getName(): string;

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Uuid
    {
        $className = $this->typeClassName();
        if ($value instanceof $className) {
            return $value;
        }

        $string = parent::convertToPHPValue($value, $platform);
        if (!is_string($string) || '' === $string) {
            return null;
        }

        return new $className($string);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof Uuid) {
            $value = $value->value();
        }

        $string = parent::convertToDatabaseValue($value, $platform);

        return is_string($string) ? $string : null;
    }

    /** @param array<string, mixed> $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 36;

        return $platform->getStringTypeDeclarationSQL($column);
    }
}
