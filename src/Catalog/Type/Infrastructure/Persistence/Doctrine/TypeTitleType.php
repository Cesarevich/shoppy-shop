<?php

declare(strict_types=1);

namespace App\Catalog\Type\Infrastructure\Persistence\Doctrine;

use App\Catalog\Type\Domain\TypeTitle;
use App\Shared\Infrastructure\Persistence\Doctrine\StringValueObjectType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class TypeTitleType extends StringValueObjectType
{
    public const NAME = 'type_title';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function valueObjectClass(): string
    {
        return TypeTitle::class;
    }

    /** @param array<string, mixed> $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 255;

        return $platform->getStringTypeDeclarationSQL($column);
    }
}
