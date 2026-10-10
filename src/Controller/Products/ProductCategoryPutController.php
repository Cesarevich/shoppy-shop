<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Catalog\Category\Domain\Exception\CategoryNotExists;
use App\Catalog\Product\Application\Command\CreateProductCategoryCommand;
use App\Catalog\Product\Domain\Exception\ProductCategoryAlreadyExists;
use App\Catalog\Product\Domain\Exception\ProductCategoryTypeMismatch;
use App\Catalog\Product\Domain\Exception\ProductNotExists;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\ValueObject\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ProductCategoryPutController
{
    public function __construct(private readonly CommandBus $commandBus) {}

    #[Route('/products/{productId}/categories/{categoryId}',
        name: 'products_category_put',
        methods: ['PUT'],
        requirements: ['productId' => Uuid::PATTERN, 'categoryId' => Uuid::PATTERN]
    )]
    public function __invoke(string $productId, string $categoryId): Response
    {
        try {
            $this->commandBus->dispatch(
                new CreateProductCategoryCommand(
                    $productId,
                    $categoryId,
                ),
            );
        } catch (ProductNotExists) {
            return new JsonResponse(['error' => 'product_not_exists'], Response::HTTP_BAD_REQUEST);
        } catch (CategoryNotExists) {
            return new JsonResponse(['error' => 'category_not_exists'], Response::HTTP_BAD_REQUEST);
        } catch (ProductCategoryTypeMismatch) {
            return new JsonResponse(['error' => 'product_category_type_mismatch'], Response::HTTP_BAD_REQUEST);
        } catch (ProductCategoryAlreadyExists) {
            return new JsonResponse(['error' => 'product_category_already_exists'], Response::HTTP_CONFLICT);
        }

        return new Response('', Response::HTTP_CREATED);
    }
}

