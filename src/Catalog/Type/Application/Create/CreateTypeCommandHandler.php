<?php

declare(strict_types=1);

namespace App\Catalog\Type\Application\Create;

use App\Catalog\Type\Domain\TypeId;
use App\Catalog\Type\Domain\TypeCode;
use App\Catalog\Type\Domain\TypeTitle;
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
