<?php

declare(strict_types=1);

namespace App\Tests\Controller\Products;

final class ProductsListGetControllerTest extends ProductsWebTestCase
{
    public function testReturnsPersistedProducts(): void
    {
        $first = $this->storeProduct();
        $second = $this->storeProduct();

        $this->client->jsonRequest('GET', '/products');

        self::assertResponseStatusCodeSame(200);
        $data = $this->responseData();
        self::assertIsList($data);

        $ids = array_column($data, 'id');
        self::assertContains($first->id()->value(), $ids);
        self::assertContains($second->id()->value(), $ids);
    }
}
