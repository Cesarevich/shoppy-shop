<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller\Category;

use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Type\Domain\ValueObject\TypeId;

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

    public function testRejectsWhenCategoryAlreadyExists(): void
    {
        $type = $this->storeType();
        $category = $this->storeCategory($type->id());

        $this->client->jsonRequest('PUT', '/categories/' . $category->id()->value(), [
            'title' => 'Другое название',
            'typeId' => $type->id()->value(),
        ]);

        self::assertResponseStatusCodeSame(409);
        self::assertSame('category_already_exists', $this->responseData()['error']);
    }

    public function testRejectsWhenTypeDoesNotExist(): void
    {
        $this->client->jsonRequest('PUT', '/categories/' . CategoryId::random()->value(), [
            'title' => 'Художественная литература',
            'typeId' => TypeId::random()->value(),
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertSame('type_not_exists', $this->responseData()['error']);
    }

    public function testRejectsWhenParentDoesNotExist(): void
    {
        $type = $this->storeType();

        $this->client->jsonRequest('PUT', '/categories/' . CategoryId::random()->value(), [
            'title' => 'Детектив',
            'typeId' => $type->id()->value(),
            'parentId' => CategoryId::random()->value(),
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertSame('category_not_exists', $this->responseData()['error']);
    }

    public function testRejectsWhenParentTypeDiffers(): void
    {
        $type = $this->storeType();
        $parent = $this->storeCategory($this->storeType()->id());

        $this->client->jsonRequest('PUT', '/categories/' . CategoryId::random()->value(), [
            'title' => 'Детектив',
            'typeId' => $type->id()->value(),
            'parentId' => $parent->id()->value(),
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertSame('category_parent_type_mismatch', $this->responseData()['error']);
    }
}
