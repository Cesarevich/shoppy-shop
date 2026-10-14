<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller\Products;

use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Tests\Mother\Catalog\Product\CreateProductCommandMother;

final class ProductsPutControllerTest extends ProductsWebTestCase
{
    public function testCreatesAndFetchesProduct(): void
    {
        $type = $this->storeType();
        $id = ProductId::random()->value();
        $command = CreateProductCommandMother::create(id: $id, typeId: $type->id()->value());

        $this->client->jsonRequest('PUT', '/products/' . $id, self::productPayload($command));
        self::assertResponseStatusCodeSame(201);

        $this->client->jsonRequest('GET', '/products/' . $id);
        self::assertResponseStatusCodeSame(200);
        self::assertJsonStringEqualsJsonString(
            json_encode([
                'id' => $id,
                'typeId' => $command->typeId(),
                'title' => $command->title(),
                'ean' => $command->ean(),
                'description' => $command->description(),
                'year' => $command->year(),
                'weight' => $command->weight(),
                'length' => $command->length(),
                'width' => $command->width(),
                'height' => $command->height(),
                'listingStatus' => ListingStatus::Draft,
                'listPriceAmount' => $command->listPriceAmount(),
                'listPriceCurrency' => $command->listPriceCurrency(),
            ], JSON_THROW_ON_ERROR),
            (string) $this->client->getResponse()->getContent(),
        );
    }

    public function testRejectsWhenTypeDoesNotExist(): void
    {
        $id = ProductId::random()->value();
        $command = CreateProductCommandMother::create(id: $id, typeId: TypeId::random()->value());

        $this->client->jsonRequest('PUT', '/products/' . $id, self::productPayload($command));

        self::assertResponseStatusCodeSame(400);
        self::assertJsonStringEqualsJsonString(
            '{"error":"type_not_exists"}',
            (string) $this->client->getResponse()->getContent(),
        );
    }

    public function testRejectsWhenProductAlreadyExists(): void
    {
        $product = $this->storeProduct();
        $command = CreateProductCommandMother::create(
            id: $product->id()->value(),
            typeId: $product->typeId()->value(),
        );

        $this->client->jsonRequest('PUT', '/products/' . $product->id()->value(), self::productPayload($command));

        self::assertResponseStatusCodeSame(409);
        self::assertJsonStringEqualsJsonString(
            '{"error":"product_already_exists"}',
            (string) $this->client->getResponse()->getContent(),
        );
    }

}
