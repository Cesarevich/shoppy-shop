<?php

declare(strict_types=1);

namespace App\Catalog\Type\Application\Create;

use App\Shared\Domain\Bus\Command\Command;

final readonly class CreateTypeCommand implements Command
{
    public function __construct(
        private string $id,
        private string $code,
        private string $title,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function title(): string
    {
        return $this->title;
    }
}
