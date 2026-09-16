<?php

declare(strict_types=1);

namespace App\Catalog\Type\Domain;

use App\Shared\Domain\DomainError;

final class TypeCodeAlreadyExists extends DomainError
{
    public function __construct(private readonly TypeCode $code)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'type_code_already_exists';
    }

    protected function errorMessage(): string
    {
        return sprintf('The type code <%s> already exists', $this->code->value());
    }
}
