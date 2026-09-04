<?php

declare(strict_types=1);

namespace App\Catalog\Product\Infrastructure\Persistence\Doctrine;

use App\Catalog\Product\Domain\ProductDescription;
use App\Shared\Infrastructure\Persistence\Doctrine\StringValueObjectType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class ProductDescriptionType extends StringValueObjectType
{
    public const NAME = 'product_description';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function valueObjectClass(): string
    {
        return ProductDescription::class;
    }

    /** @param array<string, mixed> $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getClobTypeDeclarationSQL($column);
    }
}
