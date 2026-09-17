<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use App\Shared\Domain\DomainError;

final class ProductNotWithdrawn extends DomainError
{
    public function __construct(private readonly ProductId $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'product_not_withdrawn';
    }

    protected function errorMessage(): string
    {
        return sprintf('The product <%s> is not withdrawn', $this->id->value());
    }
}
