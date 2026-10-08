<?php

declare(strict_types=1);

namespace App\Catalog\Category\Infrastructure\Persistence\Doctrine;

use App\Catalog\Category\Domain\CategoryTitle;
use App\Shared\Infrastructure\Persistence\Doctrine\StringValueObjectType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class CategoryTitleType extends StringValueObjectType
{
    public const NAME = 'category_title';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function valueObjectClass(): string
    {
        return CategoryTitle::class;
    }

    /** @param array<string, mixed> $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 255;

        return $platform->getStringTypeDeclarationSQL($column);
    }
}
