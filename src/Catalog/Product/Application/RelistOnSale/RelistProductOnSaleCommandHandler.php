<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\RelistOnSale;

use App\Catalog\Product\Domain\Exception\ProductNotExists;
use App\Catalog\Product\Domain\Repository\ProductRepository;
use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class RelistProductOnSaleCommandHandler implements CommandHandler
{
    public function __construct(
        private ProductRepository $repository,
        private EventBus $bus,
    ) {}

    public function __invoke(RelistProductOnSaleCommand $command): void
    {
        $productId = new ProductId($command->id());
        $product = $this->repository->search($productId);
        if (null === $product) {
            throw new ProductNotExists($productId);
        }

        $product->relistOnSale();
        $this->repository->save($product);
        $this->bus->publish(...$product->pullDomainEvents());
    }
}
