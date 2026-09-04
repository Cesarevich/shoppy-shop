<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Command;

use RuntimeException;

final class CommandNotRegisteredError extends RuntimeException
{
    public function __construct(Command $command)
    {
        parent::__construct(sprintf('The command <%s> has no associated handler', $command::class));
    }
}
