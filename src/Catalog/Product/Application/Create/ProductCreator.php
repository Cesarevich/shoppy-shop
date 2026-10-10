<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Create;

use App\Catalog\Product\Domain\Exception\ProductAlreadyExists;
use App\Catalog\Product\Domain\Product;
use App\Catalog\Product\Domain\ProductDetails;
use App\Catalog\Product\Domain\Repository\ProductRepository;
use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Catalog\Type\Domain\Exception\TypeNotExist;
use App\Catalog\Type\Domain\Repository\TypeRepository;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class ProductCreator
{
    public function __construct(
        private ProductRepository $repository,
        private TypeRepository $types,
        private EventBus $bus,
    ) {}

    public function __invoke(
        ProductId $id,
        TypeId $typeId,
        ProductDetails $productDetails,
    ): void {
        if (null === $this->types->search($typeId)) {
            throw new TypeNotExist($typeId);
        }

        $product = $this->repository->search($id);

        if (null !== $product) {
            throw new ProductAlreadyExists($id);
        }

        $product = Product::create(
            $id,
            $typeId,
            $productDetails,
        );

        $this->repository->save($product);
        $this->bus->publish(...$product->pullDomainEvents());
    }
}
