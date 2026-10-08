<?php

declare(strict_types=1);

namespace App\Catalog\Type\Application\Handler;

use App\Catalog\Type\Application\Command\CreateTypeCommand;
use App\Catalog\Type\Application\Service\TypeCreator;
use App\Catalog\Type\Domain\ValueObject\TypeCode;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Catalog\Type\Domain\ValueObject\TypeTitle;
use App\Shared\Domain\Bus\Command\CommandHandler;

final readonly class CreateTypeCommandHandler implements CommandHandler
{
    public function __construct(private TypeCreator $creator) {}

    public function __invoke(CreateTypeCommand $command): void
    {
        $this->creator->__invoke(
            new TypeId($command->id()),
            new TypeCode($command->code()),
            new TypeTitle($command->title()),
        );
    }
}
