<?php

declare(strict_types=1);

namespace App\Tests\Controller\Products;

use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\Product\Domain\ProductId;

final class ProductsRelistPostControllerTest extends ProductsWebTestCase
{
    public function testRelistsWithdrawnProduct(): void
    {
        $product = $this->givenProduct(ListingStatus::Withdrawn);

        $this->client->jsonRequest('POST', '/products/' . $product->id()->value() . '/relist');

        self::assertResponseStatusCodeSame(204);
        self::assertSame(ListingStatus::OnSale, $this->listingStatusOf($product->id()));
    }

    public function testRejectsWhenAlreadyOnSale(): void
    {
        $product = $this->givenProduct(ListingStatus::OnSale);

        $this->client->jsonRequest('POST', '/products/' . $product->id()->value() . '/relist');

        self::assertResponseStatusCodeSame(409);
        self::assertSame('product_already_on_sale', $this->responseData()['error']);
    }

    public function testRejectsWhenNotWithdrawn(): void
    {
        $product = $this->givenProduct(ListingStatus::Draft);

        $this->client->jsonRequest('POST', '/products/' . $product->id()->value() . '/relist');

        self::assertResponseStatusCodeSame(409);
        self::assertSame('product_not_withdrawn', $this->responseData()['error']);
    }

    public function testRejectsWhenProductDoesNotExist(): void
    {
        $this->client->jsonRequest('POST', '/products/' . ProductId::random()->value() . '/relist');

        self::assertResponseStatusCodeSame(404);
        self::assertSame('product_not_exist', $this->responseData()['error']);
    }
}
