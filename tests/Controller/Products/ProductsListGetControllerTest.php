<?php

declare(strict_types=1);

namespace App\Tests\Controller\Products;

use App\Catalog\Product\Domain\ProductId;
use App\Tests\Catalog\Product\Application\Create\CreateProductCommandMother;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProductsListGetControllerTest extends WebTestCase
{
    #[Test]
    public function it_should_list_created_products(): void
    {
        $firstId = ProductId::random()->value();
        $secondId = ProductId::random()->value();
        $client = self::createClient();

        $client->jsonRequest('PUT', '/products/' . $firstId, self::payload($firstId, 'First book'));
        self::assertResponseStatusCodeSame(201);

        $client->jsonRequest('PUT', '/products/' . $secondId, self::payload($secondId, 'Second book'));
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
    private static function payload(string $id, string $title): array
    {
        $command = CreateProductCommandMother::create(id: $id, title: $title);

        return [
            'type' => $command->type(),
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
}
