<?php

declare(strict_types=1);

namespace App\Tests\Controller\Category;

use App\Catalog\Category\Domain\ValueObject\CategoryId;

/**
 * TODO: удалить.
 *
 * Здесь проверяется только HTTP: маршрут отвечает 201. Правила создания полки это не покрывает.
 * Корень и дочерняя полка уже проверяются в
 * tests/Catalog/Category/Application/Create/CreateCategoryCommandHandlerTest.php.
 */
final class CategoriesPutControllerTest extends CategoriesWebTestCase
{
    public function testCreatesRootCategory(): void
    {
        $type = $this->storeType();
        $id = CategoryId::random()->value();
        $title = 'Художественная литература';

        $this->client->jsonRequest('PUT', '/categories/' . $id, [
            'title' => $title,
            'typeId' => $type->id()->value(),
        ]);

        self::assertResponseStatusCodeSame(201);
        $this->assertStoredCategory($id, $title, $type->id(), null);
    }

    public function testCreatesChildCategory(): void
    {
        $type = $this->storeType();
        $parent = $this->storeCategory($type->id());
        $id = CategoryId::random()->value();
        $title = 'Детектив';

        $this->client->jsonRequest('PUT', '/categories/' . $id, [
            'title' => $title,
            'typeId' => $type->id()->value(),
            'parentId' => $parent->id()->value(),
        ]);

        self::assertResponseStatusCodeSame(201);
        $this->assertStoredCategory($id, $title, $type->id(), $parent->id());
    }
}
