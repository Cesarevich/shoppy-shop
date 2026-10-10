<?php

declare(strict_types=1);

namespace App\Controller\Category;

use App\Catalog\Category\Application\Command\CreateCategoryCommand;
use App\Catalog\Category\Domain\Exception\CategoryAlreadyExists;
use App\Catalog\Category\Domain\Exception\CategoryNotExists;
use App\Catalog\Category\Domain\Exception\CategoryParentTypeMismatch;
use App\Catalog\Type\Domain\Exception\TypeNotExists;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\ValueObject\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class CategoriesPutController // toDo check (s) in TypesPutController
{
    public function __construct(private readonly CommandBus $commandBus) {}

    #[Route('/categories/{id}', name: 'categories_put', methods: ['PUT'], requirements: ['id' => Uuid::PATTERN])]
    public function __invoke(string $id, Request $request): Response
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();

        try {
            $this->commandBus->dispatch(
                new CreateCategoryCommand(
                    $id,
                    self::stringFrom($payload, 'title'),
                    self::stringFrom($payload, 'typeId'),
                    self::nullableStringFrom($payload, 'parentId'),
                ),
            );
        } catch (CategoryAlreadyExists) {
            return new JsonResponse(['error' => 'category_already_exists'], Response::HTTP_CONFLICT);
        } catch (TypeNotExists) {
            return new JsonResponse(['error' => 'type_not_exists'], Response::HTTP_BAD_REQUEST);
        } catch (CategoryNotExists) {
            return new JsonResponse(['error' => 'category_not_exists'], Response::HTTP_BAD_REQUEST);
        } catch (CategoryParentTypeMismatch) {
            return new JsonResponse(['error' => 'category_parent_type_mismatch'], Response::HTTP_BAD_REQUEST);
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
}
