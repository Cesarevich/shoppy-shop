<?php

declare(strict_types=1);

namespace App\Catalog\Type\Domain;

use App\Shared\Domain\DomainError;

final class TypeNotExist extends DomainError
{
    public function __construct(private readonly TypeId $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'type_not_exist';
    }

    protected function errorMessage(): string
    {
        return sprintf('The type <%s> does not exist', $this->id->value());
    }
}
