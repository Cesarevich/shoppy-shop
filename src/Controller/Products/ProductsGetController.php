<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Catalog\Product\Application\Find\FindProductQuery;
use App\Catalog\Product\Application\Find\ProductResponse;
use App\Catalog\Product\Domain\ProductNotExist;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Domain\ValueObject\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ProductsGetController
{
    public function __construct(private readonly QueryBus $queryBus) {}

    #[Route('/products/{id}', name: 'products_get', methods: ['GET'], requirements: ['id' => Uuid::PATTERN])]
    public function __invoke(string $id): Response
    {
        try {
            $response = $this->queryBus->ask(new FindProductQuery($id));
        } catch (ProductNotExist) {
            return new JsonResponse(['error' => 'product_not_exist'], Response::HTTP_NOT_FOUND);
        }

        if (!$response instanceof ProductResponse) {
            return new JsonResponse(['error' => 'product_not_exist'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($response->toArray());
    }
}
