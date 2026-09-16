<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Catalog\Product\Application\ListOnSale\ListProductOnSaleCommand;
use App\Catalog\Product\Domain\ProductAlreadyOnSale;
use App\Catalog\Product\Domain\ProductNotExist;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\ValueObject\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ProductsOnSalePostController
{
    public function __construct(private readonly CommandBus $commandBus) {}

    #[Route('/products/{id}/list', name: 'products_post_list', methods: ['POST'], requirements: ['id' => Uuid::PATTERN])]
    public function __invoke(string $id): Response
    {
        try {
            $this->commandBus->dispatch(
                new ListProductOnSaleCommand(
                    $id,
                ),
            );
        } catch (ProductNotExist) {
            return new JsonResponse(['error' => 'product_not_exist'], Response::HTTP_NOT_FOUND);
        } catch (ProductAlreadyOnSale) {
            return new JsonResponse(['error' => 'product_already_on_sale'], Response::HTTP_CONFLICT);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
