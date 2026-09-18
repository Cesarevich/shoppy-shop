<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Application\List;

use App\Catalog\Product\Application\List\FindProductListQuery;
use App\Catalog\Product\Application\List\FindProductListQueryHandler;
use App\Catalog\Product\Application\List\ProductListFinder;
use App\Catalog\Product\Domain\ProductRepository;
use App\Catalog\Product\Domain\Products;
use App\Tests\Catalog\Product\Domain\ProductMother;
use PHPUnit\Framework\TestCase;

final class FindProductListQueryHandlerTest extends TestCase
{
    public function testReturnsEmptyListWhenThereAreNoProducts(): void
    {
        $repository = $this->createMock(ProductRepository::class);
        $repository->expects($this->once())
            ->method('searchAll')
            ->willReturn(new Products([]));

        $handler = new FindProductListQueryHandler(new ProductListFinder($repository));
        $response = $handler->__invoke(new FindProductListQuery());

        self::assertSame([], $response->toArray());
    }

    public function testReturnsAllProducts(): void
    {
        $first = ProductMother::create();
        $second = ProductMother::create();

        $repository = $this->createMock(ProductRepository::class);
        $repository->expects($this->once())
            ->method('searchAll')
            ->willReturn(new Products([$first, $second]));

        $handler = new FindProductListQueryHandler(new ProductListFinder($repository));
        $response = $handler->__invoke(new FindProductListQuery());

        self::assertSame(
            [$first->id()->value(), $second->id()->value()],
            array_column($response->toArray(), 'id'),
        );
    }
}
