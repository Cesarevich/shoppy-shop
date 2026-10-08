<?php

declare(strict_types=1);

namespace App\Catalog\Category\Domain\Exception;

use App\Catalog\Category\Domain\CategoryId;
use App\Shared\Domain\DomainError;

final class CategoryNotExist extends DomainError
{
    public function __construct(private readonly CategoryId $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'category_not_exist';
    }

    protected function errorMessage(): string
    {
        return sprintf('The category <%s> does not exist', $this->id->value());
    }
}
