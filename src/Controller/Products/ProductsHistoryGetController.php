<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Catalog\Product\Domain\ProductId;
use App\Catalog\ProductHistory\Application\Find\FindProductHistoryQuery;
use App\Catalog\ProductHistory\Application\Find\ProductHistoriesResponse;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Domain\ValueObject\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ProductsHistoryGetController
{
    public function __construct(private readonly QueryBus $queryBus) {}

    #[Route('/products/{id}/history', name: 'products_history_get', methods: ['GET'], requirements: ['id' => Uuid::PATTERN])]
    public function __invoke(string $id): Response
    {
        $response = $this->queryBus->ask(new FindProductHistoryQuery(new ProductId($id)));

        if (!$response instanceof ProductHistoriesResponse) {
            return new JsonResponse([], Response::HTTP_OK);
        }

        return new JsonResponse($response->toArray());
    }
}
