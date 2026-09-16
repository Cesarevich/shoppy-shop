<?php

declare(strict_types=1);

namespace App\Tests\Controller\Products;

use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Type\Domain\TypeId;
use App\Tests\Catalog\Product\Application\Create\CreateProductCommandMother;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProductsPutControllerTest extends WebTestCase
{
    #[Test]
    public function it_should_create_and_fetch_a_product(): void
    {
        $id = ProductId::random()->value();
        $typeId = TypeId::random()->value();
        $command = CreateProductCommandMother::create(id: $id, typeId: $typeId);
        $client = self::createClient();

        self::createType($client, $typeId);

        $payload = [
            'typeId' => $command->typeId(),
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

        $client->jsonRequest('PUT', '/products/' . $id, $payload);
        self::assertResponseStatusCodeSame(201);

        $client->jsonRequest('GET', '/products/' . $id);
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
            (string) $client->getResponse()->getContent(),
        );
    }

    #[Test]
    public function it_should_reject_product_when_type_does_not_exist(): void
    {
        $id = ProductId::random()->value();
        $command = CreateProductCommandMother::create(id: $id, typeId: TypeId::random()->value());
        $client = self::createClient();

        $client->jsonRequest('PUT', '/products/' . $id, [
            'typeId' => $command->typeId(),
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
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertJsonStringEqualsJsonString(
            '{"error":"type_not_exist"}',
            (string) $client->getResponse()->getContent(),
        );
    }

    private static function createType(KernelBrowser $client, string $typeId): void
    {
        $client->jsonRequest('PUT', '/types/' . $typeId, [
            'code' => 'book-' . substr($typeId, 0, 8),
            'title' => 'Книги',
        ]);
        self::assertResponseStatusCodeSame(201);
    }
}
