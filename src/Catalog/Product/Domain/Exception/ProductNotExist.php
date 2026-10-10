<?php

declare(strict_types=1);

namespace App\Catalog\Product\Domain\Exception;

use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Shared\Domain\DomainError;

final class ProductNotExist extends DomainError
{
    public function __construct(private readonly ProductId $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'product_not_exist';
    }

    protected function errorMessage(): string
    {
        return sprintf('The product <%s> does not exist', $this->id->value());
    }
}
