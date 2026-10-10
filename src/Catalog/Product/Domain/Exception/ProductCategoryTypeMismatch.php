<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain\Exception;

use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Shared\Domain\DomainError;

final class ProductCategoryTypeMismatch extends DomainError
{
    public function __construct(
        private readonly ProductId $productId,
        private readonly CategoryId $categoryId,
        private readonly TypeId $typeId,
    ) {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'product_category_type_mismatch';
    }

    protected function errorMessage(): string
    {
        return sprintf(
            'The product Id <%s> and category Id <%s> does not suit type <%s>',
            $this->productId->value(),
            $this->categoryId->value(),
            $this->typeId->value(),
        );
    }
}
