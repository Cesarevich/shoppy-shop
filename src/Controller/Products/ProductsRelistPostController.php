<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Catalog\Product\Application\RelistOnSale\RelistProductOnSaleCommand;
use App\Catalog\Product\Domain\ProductAlreadyOnSale;
use App\Catalog\Product\Domain\ProductNotExist;
use App\Catalog\Product\Domain\ProductNotWithdrawn;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\ValueObject\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class ProductsRelistPostController
{
    public function __construct(private readonly CommandBus $commandBus) {}

    #[Route('/products/{id}/relist', name: 'products_post_relist', methods: ['POST'], requirements: ['id' => Uuid::PATTERN])]
    public function __invoke(string $id): Response
    {
        try {
            $this->commandBus->dispatch(
                new RelistProductOnSaleCommand(
                    $id,
                ),
            );
        } catch (ProductNotExist) {
            return new JsonResponse(['error' => 'product_not_exist'], Response::HTTP_NOT_FOUND);
        } catch (ProductAlreadyOnSale) {
            return new JsonResponse(['error' => 'product_already_on_sale'], Response::HTTP_CONFLICT);
        } catch (ProductNotWithdrawn) {
            return new JsonResponse(['error' => 'product_not_withdrawn'], Response::HTTP_CONFLICT);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
