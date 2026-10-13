<?php

declare(strict_types=1);

namespace App\Tests\Controller\Products;

/**
 * TODO: удалить.
 *
 * Здесь проверяется только HTTP: маршрут отвечает 201. Правила посадки товара на полку это не покрывает.
 * Успех и отказы уже проверяются в
 * tests/Catalog/Product/Application/CreateProductCategory/ProductCategoryCreatorTest.php.
 */
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
}
