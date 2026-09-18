<?php

declare(strict_types=1);

namespace App\Tests\Controller\Products;

use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\Product\Domain\ProductId;

final class ProductsOnSalePostControllerTest extends ProductsWebTestCase
{
    public function testListsDraftProduct(): void
    {
        $product = $this->givenProduct(ListingStatus::Draft);

        $this->client->jsonRequest('POST', '/products/' . $product->id()->value() . '/list');

        self::assertResponseStatusCodeSame(204);
        self::assertSame(ListingStatus::OnSale, $this->listingStatusOf($product->id()));
    }

    public function testRejectsWhenAlreadyOnSale(): void
    {
        $product = $this->givenProduct(ListingStatus::OnSale);

        $this->client->jsonRequest('POST', '/products/' . $product->id()->value() . '/list');

        self::assertResponseStatusCodeSame(409);
        self::assertSame('product_already_on_sale', $this->responseData()['error']);
    }

    public function testRejectsWhenWithdrawn(): void
    {
        $product = $this->givenProduct(ListingStatus::Withdrawn);

        $this->client->jsonRequest('POST', '/products/' . $product->id()->value() . '/list');

        self::assertResponseStatusCodeSame(409);
        self::assertSame('product_not_in_draft', $this->responseData()['error']);
    }

    public function testRejectsWhenProductDoesNotExist(): void
    {
        $this->client->jsonRequest('POST', '/products/' . ProductId::random()->value() . '/list');

        self::assertResponseStatusCodeSame(404);
        self::assertSame('product_not_exist', $this->responseData()['error']);
    }
}
