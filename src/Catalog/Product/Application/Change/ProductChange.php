<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Change;

use App\Catalog\Product\Application\Find\ProductFinder;
use App\Catalog\Product\Domain\ProductDetails;
use App\Catalog\Product\Domain\Repository\ProductRepository;
use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class ProductChange
{
    public function __construct(
        private ProductRepository $repository,
        private EventBus $bus,
    ) {}

    public function __invoke(
        ProductId $id,
        ProductDetails $productDetails,
    ): void {
        // todo или лучше через конструктор? Вроде просто обёртка над методом репы и зачем усложнять в констрктор
        $product = new ProductFinder($this->repository)->__invoke($id);
        $product->change($productDetails);

        $this->repository->save($product);
        $this->bus->publish(...$product->pullDomainEvents());
    }
}
