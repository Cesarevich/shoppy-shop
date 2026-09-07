<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\List;

use App\Catalog\Product\Application\Find\ProductResponse;
use App\Catalog\Product\Domain\Product;
use App\Catalog\Product\Domain\Products;
use App\Shared\Domain\Bus\Query\Response;

final readonly class ProductListResponse implements Response
{
    /** @var list<ProductResponse> */
    private array $products;

    public function __construct(ProductResponse ...$products)
    {
        $this->products = $products;
    }

    public static function fromProducts(Products $products): self
    {
        return new self(...array_map(
            static fn(Product $product): ProductResponse => ProductResponse::fromProduct($product),
            iterator_to_array($products),
        ));
    }

    /** @return list<ProductResponse> */
    public function products(): array
    {
        return $this->products;
    }

    /** @return list<array<string, mixed>> */
    public function toArray(): array
    {
        return array_map(
            static fn(ProductResponse $product): array => $product->toArray(),
            $this->products,
        );
    }
}
