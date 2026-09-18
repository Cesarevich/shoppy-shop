<?php

declare(strict_types=1);

namespace App\Tests\Controller\Products;

use App\Catalog\Product\Application\Change\ChangeProductCommand;
use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\Product\Domain\ProductId;
use App\Tests\Catalog\Product\Application\Change\ChangeProductCommandMother;

final class ProductsPatchControllerTest extends ProductsWebTestCase
{
    public function testChangesAndFetchesProduct(): void
    {
        $product = $this->givenProduct();
        $id = $product->id()->value();
        $command = ChangeProductCommandMother::create(id: $id, title: 'Clean Architecture revised', year: 2011);

        $this->client->jsonRequest('PATCH', '/products/' . $id, self::changePayload($command));
        self::assertResponseStatusCodeSame(204);

        $this->client->jsonRequest('GET', '/products/' . $id);
        self::assertResponseStatusCodeSame(200);
        self::assertJsonStringEqualsJsonString(
            json_encode([
                'id' => $id,
                'typeId' => $product->typeId()->value(),
                'title' => 'Clean Architecture revised',
                'ean' => $command->ean(),
                'description' => $command->description(),
                'year' => 2011,
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

    public function testRejectsWhenProductDoesNotExist(): void
    {
        $id = ProductId::random()->value();
        $command = ChangeProductCommandMother::create(id: $id, title: 'Clean Architecture revised');

        $this->client->jsonRequest('PATCH', '/products/' . $id, self::changePayload($command));

        self::assertResponseStatusCodeSame(404);
        self::assertJsonStringEqualsJsonString(
            '{"error":"product_not_exist"}',
            (string) $this->client->getResponse()->getContent(),
        );
    }

    /** @return array<string, mixed> */
    private static function changePayload(ChangeProductCommand $command): array
    {
        return [
            'title' => $command->title(),
            'ean' => $command->ean(),
            'description' => $command->description(),
            'year' => $command->year(),
            'weight' => $command->weight(),
            'length' => $command->length(),
            'width' => $command->width(),
            'height' => $command->height(),
            'listPriceAmount' => $command->listPriceAmount(),
            'listPriceCurrency' => $command->listPriceCurrency(),
        ];
    }
}
