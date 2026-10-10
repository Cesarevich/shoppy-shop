<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain\Exception;

use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Shared\Domain\DomainError;

final class ProductNotOnSale extends DomainError
{
    public function __construct(private readonly ProductId $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'product_not_on_sale';
    }

    protected function errorMessage(): string
    {
        return sprintf('The product <%s> is not on sale', $this->id->value());
    }
}
