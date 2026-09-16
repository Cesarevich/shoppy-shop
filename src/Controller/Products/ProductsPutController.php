<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Catalog\Product\Application\Create\CreateProductCommand;
use App\Catalog\Product\Domain\ProductAlreadyExists;
use App\Catalog\Type\Domain\TypeNotExist;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\ValueObject\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ProductsPutController
{
    public function __construct(private readonly CommandBus $commandBus) {}

    #[Route('/products/{id}', name: 'products_put', methods: ['PUT'], requirements: ['id' => Uuid::PATTERN])]
    public function __invoke(string $id, Request $request): Response
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();

        try {
            $this->commandBus->dispatch(
                new CreateProductCommand(
                    $id,
                    self::stringFrom($payload, 'typeId'),
                    self::stringFrom($payload, 'title'),
                    self::nullableStringFrom($payload, 'ean'),
                    self::nullableStringFrom($payload, 'description'),
                    self::nullableIntFrom($payload, 'year'),
                    self::nullableIntFrom($payload, 'weight'),
                    self::nullableIntFrom($payload, 'length'),
                    self::nullableIntFrom($payload, 'width'),
                    self::nullableIntFrom($payload, 'height'),
                    self::intFrom($payload, 'listPriceAmount'),
                    self::stringFrom($payload, 'listPriceCurrency', 'BYN'),
                ),
            );
        } catch (TypeNotExist) {
            return new JsonResponse(['error' => 'type_not_exist'], Response::HTTP_BAD_REQUEST);
        } catch (ProductAlreadyExists) {
            return new JsonResponse(['error' => 'product_already_exists'], Response::HTTP_CONFLICT);
        }

        return new Response('', Response::HTTP_CREATED);
    }

    /** @param array<string, mixed> $payload */
    private static function stringFrom(array $payload, string $key, string $default = ''): string
    {
        $value = $payload[$key] ?? $default;

        return is_string($value) ? $value : $default;
    }

    /** @param array<string, mixed> $payload */
    private static function nullableStringFrom(array $payload, string $key): ?string
    {
        $value = $payload[$key] ?? null;
        if (null === $value || '' === $value) {
            return null;
        }

        return is_string($value) ? $value : null;
    }

    /** @param array<string, mixed> $payload */
    private static function intFrom(array $payload, string $key): int
    {
        $value = $payload[$key] ?? 0;
        if (is_int($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }

        return 0;
    }

    /** @param array<string, mixed> $payload */
    private static function nullableIntFrom(array $payload, string $key): ?int
    {
        $value = $payload[$key] ?? null;
        if (null === $value || '' === $value) {
            return null;
        }
        if (is_int($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }
}
