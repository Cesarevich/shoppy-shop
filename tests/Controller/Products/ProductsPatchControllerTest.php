<?php

declare(strict_types=1);

namespace App\Tests\Controller\Products;

use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Type\Domain\TypeId;
use App\Tests\Catalog\Product\Application\Create\CreateProductCommandMother;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProductsPatchControllerTest extends WebTestCase
{
    #[Test]
    public function it_should_change_and_fetch_a_product(): void
    {
        $id = ProductId::random()->value();
        $typeId = TypeId::random()->value();
        $command = CreateProductCommandMother::create(id: $id, typeId: $typeId);
        $client = self::createClient();

        self::createType($client, $typeId);

        $createPayload = [
            'typeId' => $command->typeId(),
            'title' => $command->title(),
            'ean' => $command->ean(),
            'description' => $command->description(),
            'year' => $command->year(),
            'weight' => $command->weight(),
            'length' => $command->length(),
            'width' => $command->width(),
            'height' => $command->height(),
            'listingStatus' => $command->listingStatus(),
            'listPriceAmount' => $command->listPriceAmount(),
            'listPriceCurrency' => $command->listPriceCurrency(),
        ];

        $client->jsonRequest('PUT', '/products/' . $id, $createPayload);
        self::assertResponseStatusCodeSame(201);

        $changePayload = [
            'title' => 'Clean Architecture revised',
            'ean' => $command->ean(),
            'description' => $command->description(),
            'year' => 2011,
            'weight' => $command->weight(),
            'length' => $command->length(),
            'width' => $command->width(),
            'height' => $command->height(),
            'listPriceAmount' => $command->listPriceAmount(),
            'listPriceCurrency' => $command->listPriceCurrency(),
        ];

        $client->jsonRequest('PATCH', '/products/' . $id, $changePayload);
        self::assertResponseStatusCodeSame(204);

        $client->jsonRequest('GET', '/products/' . $id);
        self::assertResponseStatusCodeSame(200);
        self::assertJsonStringEqualsJsonString(
            json_encode([
                'id' => $id,
                'typeId' => $typeId,
                'title' => 'Clean Architecture revised',
                'ean' => $command->ean(),
                'description' => $command->description(),
                'year' => 2011,
                'weight' => $command->weight(),
                'length' => $command->length(),
                'width' => $command->width(),
                'height' => $command->height(),
                'listingStatus' => $command->listingStatus(),
                'listPriceAmount' => $command->listPriceAmount(),
                'listPriceCurrency' => $command->listPriceCurrency(),
            ], JSON_THROW_ON_ERROR),
            (string) $client->getResponse()->getContent(),
        );
    }

    #[Test]
    public function it_should_reject_change_when_product_does_not_exist(): void
    {
        $id = ProductId::random()->value();
        $client = self::createClient();

        $client->jsonRequest('PATCH', '/products/' . $id, [
            'title' => 'Clean Architecture revised',
            'ean' => '9780134494166',
            'description' => 'A craftsman guide',
            'year' => 2011,
            'weight' => 500,
            'length' => 240,
            'width' => 160,
            'height' => 30,
            'listPriceAmount' => 4500,
            'listPriceCurrency' => 'BYN',
        ]);

        self::assertResponseStatusCodeSame(404);
        self::assertJsonStringEqualsJsonString(
            '{"error":"product_not_exist"}',
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
