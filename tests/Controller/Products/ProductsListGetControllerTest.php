<?php

declare(strict_types=1);

namespace App\Tests\Controller\Products;

use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Type\Domain\TypeId;
use App\Tests\Catalog\Product\Application\Create\CreateProductCommandMother;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProductsListGetControllerTest extends WebTestCase
{
    #[Test]
    public function it_should_list_created_products(): void
    {
        $firstId = ProductId::random()->value();
        $secondId = ProductId::random()->value();
        $typeId = TypeId::random()->value();
        $client = self::createClient();

        self::createType($client, $typeId);

        $client->jsonRequest('PUT', '/products/' . $firstId, self::payload($firstId, $typeId, 'First book'));
        self::assertResponseStatusCodeSame(201);

        $client->jsonRequest('PUT', '/products/' . $secondId, self::payload($secondId, $typeId, 'Second book'));
        self::assertResponseStatusCodeSame(201);

        $client->jsonRequest('GET', '/products');
        self::assertResponseStatusCodeSame(200);

        /** @var list<array<string, mixed>> $data */
        $data = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsList($data);

        $ids = array_column($data, 'id');
        self::assertContains($firstId, $ids);
        self::assertContains($secondId, $ids);
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
            'listingStatus' => $command->listingStatus(),
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
