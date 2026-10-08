<?php

declare(strict_types=1);

namespace App\Catalog\Category\Domain;

use App\Catalog\Category\Domain\Event\CategoryCreatedDomainEvent;
use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Category\Domain\ValueObject\CategoryTitle;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Shared\Domain\Aggregate\AggregateRoot;

final class Category extends AggregateRoot
{
    public function __construct(
        private readonly CategoryId $id,
        private readonly CategoryTitle $title,
        private readonly TypeId $typeId,
        private readonly ?CategoryId $parentId,
    ) {}

    public static function create(
        CategoryId $id,
        CategoryTitle $title,
        TypeId $typeId,
        ?CategoryId $parentId,
    ): self {
        $category = new self($id, $title, $typeId, $parentId);
        $category->record(CategoryCreatedDomainEvent::fromCategory($category));

        return $category;
    }

    public function id(): CategoryId
    {
        return $this->id;
    }

    public function title(): CategoryTitle
    {
        return $this->title;
    }

    public function typeId(): TypeId
    {
        return $this->typeId;
    }

    public function parentId(): ?CategoryId
    {
        return $this->parentId;
    }
}
