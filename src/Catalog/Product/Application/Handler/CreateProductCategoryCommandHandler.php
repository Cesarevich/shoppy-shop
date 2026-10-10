<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Handler;

use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Product\Application\Command\CreateProductCategoryCommand;
use App\Catalog\Product\Application\Service\ProductCategoryCreator;
use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Shared\Domain\Bus\Command\CommandHandler;

final readonly class CreateProductCategoryCommandHandler implements CommandHandler
{
    public function __construct(private ProductCategoryCreator $creator) {}

    public function __invoke(CreateProductCategoryCommand $command): void
    {
        $this->creator->__invoke(
            new ProductId($command->productId()),
            new CategoryId($command->categoryId()),
        );
    }
}
