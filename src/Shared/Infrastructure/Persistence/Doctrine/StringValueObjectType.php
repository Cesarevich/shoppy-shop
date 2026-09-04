<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\ValueObject\StringValueObject;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

abstract class StringValueObjectType extends StringType
{
    /** @return class-string<StringValueObject> */
    abstract protected function valueObjectClass(): string;

    abstract public function getName(): string;

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?StringValueObject
    {
        $className = $this->valueObjectClass();
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

        if ($value instanceof StringValueObject) {
            $value = $value->value();
        }

        $string = parent::convertToDatabaseValue($value, $platform);

        return is_string($string) ? $string : null;
    }
}
