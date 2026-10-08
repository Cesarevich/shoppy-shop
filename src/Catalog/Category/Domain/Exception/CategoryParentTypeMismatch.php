<?php

declare(strict_types=1);

namespace App\Catalog\Category\Domain\Exception;

use App\Catalog\Category\Domain\CategoryId;
use App\Catalog\Type\Domain\TypeId;
use App\Shared\Domain\DomainError;

final class CategoryParentTypeMismatch extends DomainError
{
    public function __construct(
        private readonly CategoryId $parentId,
        private readonly TypeId $typeId,
    ) {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'category_parent_type_mismatch';
    }

    protected function errorMessage(): string
    {
        return sprintf(
            'The category parent <%s> does not accept type <%s>',
            $this->parentId->value(),
            $this->typeId->value(),
        );
    }
}
