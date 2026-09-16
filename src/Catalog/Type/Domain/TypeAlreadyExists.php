<?php

declare(strict_types=1);

namespace App\Catalog\Type\Domain;

use App\Shared\Domain\DomainError;

final class TypeAlreadyExists extends DomainError
{
    public function __construct(private readonly TypeId $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'type_already_exists';
    }

    protected function errorMessage(): string
    {
        return sprintf('The type <%s> already exists', $this->id->value());
    }
}
