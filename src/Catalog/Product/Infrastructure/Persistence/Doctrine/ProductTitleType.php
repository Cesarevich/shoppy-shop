<?php

declare(strict_types=1);

namespace App\Catalog\Product\Infrastructure\Persistence\Doctrine;

use App\Catalog\Product\Domain\ProductTitle;
use App\Shared\Infrastructure\Persistence\Doctrine\StringValueObjectType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class ProductTitleType extends StringValueObjectType
{
    public const NAME = 'product_title';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function valueObjectClass(): string
    {
        return ProductTitle::class;
    }

    /** @param array<string, mixed> $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 255;

        return $platform->getStringTypeDeclarationSQL($column);
    }
}
