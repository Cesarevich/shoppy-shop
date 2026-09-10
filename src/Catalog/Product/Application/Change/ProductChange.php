<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Change;

use App\Catalog\Product\Application\Find\ProductFinder;
use App\Catalog\Product\Domain\Dimensions;
use App\Catalog\Product\Domain\Ean;
use App\Catalog\Product\Domain\Money;
use App\Catalog\Product\Domain\ProductDescription;
use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Product\Domain\ProductRepository;
use App\Catalog\Product\Domain\ProductTitle;
use App\Catalog\Product\Domain\Year;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class ProductChange
{
    public function __construct(
        private ProductRepository $repository,
        private EventBus $bus,
    ) {}

    public function __invoke(
        ProductId $id,
        ProductTitle $title,
        ?Ean $ean,
        ?ProductDescription $description,
        ?Year $year,
        ?Dimensions $dimensions,
        Money $listPrice,
    ): void {
        // todo или лучше через конструктор? Вроде просто обёртка над методом репы и зачем усложнять в констрктор
        $product = (new ProductFinder($this->repository))->__invoke($id);
        $product->change($title, $ean, $description, $year, $dimensions, $listPrice);

        $this->repository->save($product);
        $this->bus->publish(...$product->pullDomainEvents());
    }
}
