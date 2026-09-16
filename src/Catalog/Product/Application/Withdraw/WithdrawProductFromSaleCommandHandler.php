<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Withdraw;

use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Product\Domain\ProductNotExist;
use App\Catalog\Product\Domain\ProductRepository;
use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class ProductWithdrawCommandHandler implements CommandHandler
{
    public function __construct(
        private ProductRepository $repository,
        private EventBus $bus,
    ) {}

    public function __invoke(ProductWithdrawCommand $command): void
    {
        $productId = new ProductId($command->id());
        $product = $this->repository->search($productId);
        if (null === $product) {
            throw new ProductNotExist($productId);
        }

        $product->withdraw();
        $this->repository->save($product);
        $this->bus->publish(...$product->pullDomainEvents());
    }
}
