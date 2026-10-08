<?php

declare(strict_types=1);

namespace App\Catalog\Category\Domain\Exception;

use App\Catalog\Category\Domain\CategoryId;
use App\Shared\Domain\DomainError;

final class CategoryAlreadyExists extends DomainError
{
    public function __construct(private readonly CategoryId $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'category_already_exists';
    }

    protected function errorMessage(): string
    {
        return sprintf('The category <%s> already exists', $this->id->value());
    }
}
