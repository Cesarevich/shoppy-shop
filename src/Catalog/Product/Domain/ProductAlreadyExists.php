<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain;

use App\Shared\Domain\DomainError;

final class ProductAlreadyExists extends DomainError
{
    public function __construct(private readonly ProductId $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'product_already_exists';
    }

    protected function errorMessage(): string
    {
        return sprintf('The product <%s> already exists', $this->id->value());
    }
}
