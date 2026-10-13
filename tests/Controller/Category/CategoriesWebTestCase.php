<?php

declare(strict_types=1);

namespace App\Tests\Controller\Category;

use App\Catalog\Category\Domain\Category;
use App\Catalog\Category\Domain\Repository\CategoryRepository;
use App\Catalog\Category\Domain\ValueObject\CategoryId;
use App\Catalog\Type\Domain\Type;
use App\Catalog\Type\Domain\ValueObject\TypeId;
use App\Tests\Catalog\Category\Domain\CategoryMother;
use App\Tests\Catalog\Type\Domain\TypeMother;
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
