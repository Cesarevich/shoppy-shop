<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain\Exception;

use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Shared\Domain\DomainError;

final class ProductCategoryAlreadyExists extends DomainError
{
    public function __construct(
        private readonly ProductId $productId,
        private readonly CategoryId $categoryId,
    ) {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'product_category_already_exists';
    }

    protected function errorMessage(): string
    {
        return sprintf('The product <%s> already in category <%s>',
            $this->productId->value(),
            $this->categoryId->value()
        );
    }
}
