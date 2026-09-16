<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Catalog\Product\Application\Withdraw\WithdrawProductFromSaleCommand;
use App\Catalog\Product\Domain\ProductAlreadyWithdrawn;
use App\Catalog\Product\Domain\ProductNotExist;
use App\Catalog\Product\Domain\ProductNotOnSale;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\ValueObject\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ProductsWithdrawPostController
{
    public function __construct(private readonly CommandBus $commandBus) {}

    #[Route('/products/{id}/withdraw', name: 'products_post_withdraw', methods: ['POST'], requirements: ['id' => Uuid::PATTERN])]
    public function __invoke(string $id): Response
    {
        try {
            $this->commandBus->dispatch(
                new WithdrawProductFromSaleCommand(
                    $id,
                ),
            );
        } catch (ProductNotExist) {
            return new JsonResponse(['error' => 'product_not_exist'], Response::HTTP_NOT_FOUND);
        } catch (ProductAlreadyWithdrawn) {
            return new JsonResponse(['error' => 'product_already_withdrawn'], Response::HTTP_CONFLICT);
        } catch (ProductNotOnSale) {
            return new JsonResponse(['error' => 'product_not_on_sale'], Response::HTTP_CONFLICT);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
