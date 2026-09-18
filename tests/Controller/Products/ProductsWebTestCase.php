<?php

declare(strict_types=1);

namespace App\Tests\Controller\Products;

use App\Catalog\Product\Application\Create\CreateProductCommand;
use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\Product\Domain\Product;
use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Product\Domain\ProductRepository;
use App\Catalog\Type\Domain\Type;
use App\Tests\Catalog\Product\Domain\ProductMother;
use App\Tests\Catalog\Type\Domain\TypeMother;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ProductsWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
    }

    /** Persists a Type directly, bypassing HTTP: the test is not about creating types. */
    protected function givenType(): Type
    {
        $type = TypeMother::create();

        $this->persist($type);

        return $type;
    }

    /** Persists a Product (with its Type) already in the given listing state. */
    protected function givenProduct(ListingStatus $listingStatus = ListingStatus::Draft): Product
    {
        $product = ProductMother::create(
            typeId: $this->givenType()->id(),
            listingStatus: $listingStatus,
        );

        $this->persist($product);

        return $product;
    }

    /** Reads the persisted state after the request, bypassing the identity map. */
    protected function listingStatusOf(ProductId $id): ListingStatus
    {
        $this->entityManager()->clear();

        /** @var ProductRepository $products */
        $products = self::getContainer()->get(ProductRepository::class);
        $product = $products->search($id);
        if (null === $product) {
            throw new LogicException(sprintf('Product <%s> was not found after request.', $id->value()));
        }

        return $product->listingStatus();
    }

    /** @return array<string, mixed> */
    protected static function productPayload(CreateProductCommand $command): array
    {
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

    /** @return array<array-key, mixed> */
    protected function responseData(): array
    {
        /** @var array<array-key, mixed> $data */
        $data = json_decode((string) $this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $data;
    }

    private function persist(object $aggregate): void
    {
        $entityManager = $this->entityManager();
        $entityManager->persist($aggregate);
        $entityManager->flush();
    }

    private function entityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine')->getManager();

        return $entityManager;
    }
}
