<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller\Products;

use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Product\Domain\ValueObject\ProductId;

final class ProductsCategoryPutControllerTest extends ProductsWebTestCase
{
    public function testCreatesProductCategory(): void
    {
        $product = $this->storeProduct();
        $category = $this->storeCategory($product->typeId());

        $this->client->jsonRequest(
            'PUT',
            '/products/' . $product->id()->value() . '/categories/' . $category->id()->value(),
        );
        self::assertResponseStatusCodeSame(201);
        $this->assertStoredProductCategory($product->id(), $category->id());
    }

    public function testRejectsWhenProductDoesNotExist(): void
    {
        $category = $this->storeCategory($this->storeType()->id());

        $this->client->jsonRequest(
            'PUT',
            '/products/' . ProductId::random()->value() . '/categories/' . $category->id()->value(),
        );

        self::assertResponseStatusCodeSame(400);
        self::assertSame('product_not_exists', $this->responseData()['error']);
    }

    public function testRejectsWhenCategoryDoesNotExist(): void
    {
        $product = $this->storeProduct();

        $this->client->jsonRequest(
            'PUT',
            '/products/' . $product->id()->value() . '/categories/' . CategoryId::random()->value(),
        );

        self::assertResponseStatusCodeSame(400);
        self::assertSame('category_not_exists', $this->responseData()['error']);
    }

    public function testRejectsWhenTypesDiffer(): void
    {
        $product = $this->storeProduct();
        $category = $this->storeCategory($this->storeType()->id());

        $this->client->jsonRequest(
            'PUT',
            '/products/' . $product->id()->value() . '/categories/' . $category->id()->value(),
        );

        self::assertResponseStatusCodeSame(400);
        self::assertSame('product_category_type_mismatch', $this->responseData()['error']);
    }

    public function testRejectsWhenProductCategoryAlreadyExists(): void
    {
        $product = $this->storeProduct();
        $category = $this->storeCategory($product->typeId());
        $uri = '/products/' . $product->id()->value() . '/categories/' . $category->id()->value();

        $this->client->jsonRequest('PUT', $uri);
        self::assertResponseStatusCodeSame(201);

        $this->client->jsonRequest('PUT', $uri);

        self::assertResponseStatusCodeSame(409);
        self::assertSame('product_category_already_exists', $this->responseData()['error']);
    }
}
