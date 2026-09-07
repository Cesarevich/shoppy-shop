<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Application\List;

use App\Catalog\Product\Application\List\FindProductListQuery;
use App\Catalog\Product\Application\List\FindProductListQueryHandler;
use App\Catalog\Product\Application\List\ProductListFinder;
use App\Catalog\Product\Domain\ProductRepository;
use App\Catalog\Product\Domain\Products;
use App\Tests\Catalog\Product\Application\Create\CreateProductCommandMother;
use App\Tests\Catalog\Product\Domain\ProductMother;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FindProductListQueryHandlerTest extends TestCase
{
    #[Test]
    public function it_should_return_an_empty_list_when_there_are_no_products(): void
    {
        $repository = $this->createMock(ProductRepository::class);
        $repository->expects($this->once())
            ->method('searchAll')
            ->willReturn(new Products([]));

        $handler = new FindProductListQueryHandler(new ProductListFinder($repository));
        $response = $handler->__invoke(new FindProductListQuery());

        self::assertSame([], $response->toArray());
    }

    #[Test]
    public function it_should_return_all_products(): void
    {
        $first = ProductMother::fromCommand(CreateProductCommandMother::create(
            id: '1c8f8d2e-6b1a-4f3c-9e7d-2a4b6c8d0e11',
            title: 'First book',
        ));
        $second = ProductMother::fromCommand(CreateProductCommandMother::create(
            id: '1c8f8d2e-6b1a-4f3c-9e7d-2a4b6c8d0e12',
            title: 'Second book',
        ));

        $repository = $this->createMock(ProductRepository::class);
        $repository->expects($this->once())
            ->method('searchAll')
            ->willReturn(new Products([$first, $second]));

        $handler = new FindProductListQueryHandler(new ProductListFinder($repository));
        $response = $handler->__invoke(new FindProductListQuery());

        $ids = array_column($response->toArray(), 'id');
        self::assertSame(
            ['1c8f8d2e-6b1a-4f3c-9e7d-2a4b6c8d0e11', '1c8f8d2e-6b1a-4f3c-9e7d-2a4b6c8d0e12'],
            $ids,
        );
    }
}
