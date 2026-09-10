<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Catalog\Product\Application\Change\ChangeProductCommand;
use App\Catalog\Product\Domain\ProductNotExist;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\ValueObject\Uuid;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ProductsPatchController
{
    public function __construct(private readonly CommandBus $commandBus) {}

    #[Route('/products/{id}', name: 'products_patch', methods: ['PATCH'], requirements: ['id' => Uuid::PATTERN])]
    public function __invoke(string $id, Request $request): Response
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();

        try {
            $this->commandBus->dispatch(
                new ChangeProductCommand(
                    $id,
                    self::requiredStringFrom($payload, 'title'),
                    self::nullableStringFrom($payload, 'ean'),
                    self::nullableStringFrom($payload, 'description'),
                    self::nullableIntFrom($payload, 'year'),
                    self::nullableIntFrom($payload, 'weight'),
                    self::nullableIntFrom($payload, 'length'),
                    self::nullableIntFrom($payload, 'width'),
                    self::nullableIntFrom($payload, 'height'),
                    self::requiredIntFrom($payload, 'listPriceAmount'),
                    self::requiredStringFrom($payload, 'listPriceCurrency'),
                ),
            );
        } catch (ProductNotExist) {
            return new JsonResponse(['error' => 'product_not_exist'], Response::HTTP_NOT_FOUND);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /** @param array<string, mixed> $payload */
    private static function requiredStringFrom(array $payload, string $key): string
    {
        if (!array_key_exists($key, $payload) || !is_string($payload[$key]) || '' === $payload[$key]) {
            throw new InvalidArgumentException(sprintf('<%s> is required.', $key));
        }

        return $payload[$key];
    }

    /** @param array<string, mixed> $payload */
    private static function requiredIntFrom(array $payload, string $key): int
    {
        if (!array_key_exists($key, $payload)) {
            throw new InvalidArgumentException(sprintf('<%s> is required.', $key));
        }

        $value = $payload[$key];
        if (is_int($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }

        throw new InvalidArgumentException(sprintf('<%s> is required.', $key));
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
