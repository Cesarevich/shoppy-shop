<?php

declare(strict_types=1);

namespace App\Tests\Controller\Products;

use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Type\Domain\TypeId;
use App\Tests\Catalog\Product\Application\Create\CreateProductCommandMother;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProductsWithdrawPostControllerTest extends WebTestCase
{
    #[Test]
    public function it_should_withdraw_a_product(): void
    {
        $productId = ProductId::random()->value();
        $typeId = TypeId::random()->value();
        $client = self::createClient();

        self::createType($client, $typeId);

        $client->jsonRequest('PUT', '/products/' . $productId, self::payload($productId, $typeId, 'A product'));
        self::assertResponseStatusCodeSame(201);

        $client->jsonRequest('POST', '/products/' . $productId . '/list');
        self::assertResponseStatusCodeSame(204);

        $client->jsonRequest('POST', '/products/' . $productId . '/withdraw');
        self::assertResponseStatusCodeSame(204);

        $client->jsonRequest('GET', '/products/' . $productId);
        self::assertResponseStatusCodeSame(200);

        /** @var array<string, mixed> $data */
        $data = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame($productId, $data['id']);
        self::assertSame('withdrawn', $data['listingStatus']);
    }

    #[Test]
    public function it_should_reject_product_withdraw_if_already_withdrawn(): void
    {
        $productId = ProductId::random()->value();
        $typeId = TypeId::random()->value();
        $client = self::createClient();

        self::createType($client, $typeId);

        $client->jsonRequest('PUT', '/products/' . $productId, self::payload($productId, $typeId, 'A product'));
        self::assertResponseStatusCodeSame(201);

        $client->jsonRequest('POST', '/products/' . $productId . '/list');
        self::assertResponseStatusCodeSame(204);

        $client->jsonRequest('POST', '/products/' . $productId . '/withdraw');
        self::assertResponseStatusCodeSame(204);

        $client->jsonRequest('POST', '/products/' . $productId . '/withdraw');
        self::assertResponseStatusCodeSame(409);

        $data = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('product_already_withdrawn', $data['error']);
    }

    #[Test]
    public function it_should_reject_if_product_not_on_sale(): void
    {
        $productId = ProductId::random()->value();
        $typeId = TypeId::random()->value();
        $client = self::createClient();

        self::createType($client, $typeId);

        $client->jsonRequest('PUT', '/products/' . $productId, self::payload($productId, $typeId, 'A product'));
        self::assertResponseStatusCodeSame(201);

        $client->jsonRequest('POST', '/products/' . $productId . '/withdraw');
        self::assertResponseStatusCodeSame(409);

        $data = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('product_not_on_sale', $data['error']);
    }

    #[Test]
    public function it_should_reject_when_product_does_not_exist(): void
    {
        $productId = ProductId::random()->value();
        $client = self::createClient();

        $client->jsonRequest('POST', '/products/' . $productId . '/withdraw');
        self::assertResponseStatusCodeSame(404);

        $data = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('product_not_exist', $data['error']);
    }

    /** @return array<string, mixed> */
    private static function payload(string $id, string $typeId, string $title): array
    {
        $command = CreateProductCommandMother::create(id: $id, typeId: $typeId, title: $title);

        return [
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
