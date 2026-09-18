<?php

declare(strict_types=1);

namespace App\Tests\Controller\Products;

use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\Product\Domain\ProductId;

final class ProductsWithdrawPostControllerTest extends ProductsWebTestCase
{
    public function testWithdrawsProductOnSale(): void
    {
        $product = $this->givenProduct(ListingStatus::OnSale);

        $this->client->jsonRequest('POST', '/products/' . $product->id()->value() . '/withdraw');

        self::assertResponseStatusCodeSame(204);
        self::assertSame(ListingStatus::Withdrawn, $this->listingStatusOf($product->id()));
    }

    public function testRejectsWhenAlreadyWithdrawn(): void
    {
        $product = $this->givenProduct(ListingStatus::Withdrawn);

        $this->client->jsonRequest('POST', '/products/' . $product->id()->value() . '/withdraw');

        self::assertResponseStatusCodeSame(409);
        self::assertSame('product_already_withdrawn', $this->responseData()['error']);
    }

    public function testRejectsWhenNotOnSale(): void
    {
        $product = $this->givenProduct(ListingStatus::Draft);

        $this->client->jsonRequest('POST', '/products/' . $product->id()->value() . '/withdraw');

        self::assertResponseStatusCodeSame(409);
        self::assertSame('product_not_on_sale', $this->responseData()['error']);
    }

    public function testRejectsWhenProductDoesNotExist(): void
    {
        $this->client->jsonRequest('POST', '/products/' . ProductId::random()->value() . '/withdraw');

        self::assertResponseStatusCodeSame(404);
        self::assertSame('product_not_exist', $this->responseData()['error']);
    }
}
