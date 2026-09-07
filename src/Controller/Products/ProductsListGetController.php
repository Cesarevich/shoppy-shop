<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Catalog\Product\Application\List\FindProductListQuery;
use App\Catalog\Product\Application\List\ProductListResponse;
use App\Shared\Domain\Bus\Query\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ProductsListGetController
{
    public function __construct(private readonly QueryBus $queryBus) {}

    #[Route('/products', name: 'products_list_get', methods: ['GET'])]
    public function __invoke(): Response
    {
        $response = $this->queryBus->ask(new FindProductListQuery());
        if (!$response instanceof ProductListResponse) {
            return new JsonResponse([], Response::HTTP_OK);
        }

        return new JsonResponse($response->toArray());
    }
}
