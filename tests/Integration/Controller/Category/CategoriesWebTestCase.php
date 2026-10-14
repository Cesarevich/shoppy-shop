<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controller\Category;

use App\Catalog\Category\Domain\Category;
use App\Catalog\Category\Domain\Repository\CategoryRepository;
use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Type\Domain\Type;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Tests\Mother\Catalog\Category\CategoryMother;
use App\Tests\Mother\Catalog\Type\TypeMother;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class CategoriesWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
    }

    protected function storeType(): Type
    {
        $type = TypeMother::create();

        $this->persist($type);

        return $type;
    }

    protected function storeCategory(TypeId $typeId): Category
    {
        $category = CategoryMother::create(
            typeId: $typeId,
        );

        $this->persist($category);

        return $category;
    }

    protected function assertStoredCategory(
        string $id,
        string $title,
        TypeId $typeId,
        ?CategoryId $parentId,
    ): void {
        $this->entityManager()->clear();

        /** @var CategoryRepository $categories */
        $categories = self::getContainer()->get(CategoryRepository::class);
        $category = $categories->search(new CategoryId($id));
        if (null === $category) {
            throw new LogicException(sprintf('Category <%s> was not found after request.', $id));
        }

        self::assertSame($id, $category->id()->value());
        self::assertSame($title, $category->title()->value());
        self::assertTrue($category->typeId()->equals($typeId));
        self::assertSame($parentId?->value(), $category->parentId()?->value());
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
