<?php

declare(strict_types=1);

namespace App\Tests\Controller\Products;

use App\Catalog\Product\Domain\ProductId;
use App\Tests\Catalog\Product\Application\Create\CreateProductCommandMother;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProductsPutControllerTest extends WebTestCase
{
    #[Test]
    public function it_should_create_and_fetch_a_product(): void
    {
        $id = ProductId::random()->value();
        $command = CreateProductCommandMother::create(id: $id);
        $client = self::createClient();

        $payload = [
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

        $client->jsonRequest('PUT', '/products/' . $id, $payload);
        self::assertResponseStatusCodeSame(201);

        $client->jsonRequest('GET', '/products/' . $id);
        self::assertResponseStatusCodeSame(200);
        self::assertJsonStringEqualsJsonString(
            json_encode(['id' => $id, ...$payload], JSON_THROW_ON_ERROR),
            (string) $client->getResponse()->getContent(),
        );
    }
}
