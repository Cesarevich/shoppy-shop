<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Query;

use RuntimeException;

final class QueryNotRegisteredError extends RuntimeException
{
    public function __construct(Query $query)
    {
        parent::__construct(sprintf('The query <%s> has no associated handler', $query::class));
    }
}
