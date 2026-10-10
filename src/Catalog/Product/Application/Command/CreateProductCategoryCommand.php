<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Command;

use App\Shared\Domain\Bus\Command\Command;

final readonly class CreateProductCategoryCommand implements Command
{
    public function __construct(
        private string $productId,
        private string $categoryId,
    ) {}

    public function productId(): string
    {
        return $this->productId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }
}
