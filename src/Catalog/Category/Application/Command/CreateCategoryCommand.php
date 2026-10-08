<?php

declare(strict_types=1);

namespace App\Catalog\Category\Application\Command;

use App\Shared\Domain\Bus\Command\Command;

final readonly class CreateCategoryCommand implements Command
{
    public function __construct(
        private string $id,
        private string $title,
        private string $typeId,
        private ?string $parentId,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function typeId(): string
    {
        return $this->typeId;
    }

    public function parentId(): ?string
    {
        return $this->parentId;
    }
}
