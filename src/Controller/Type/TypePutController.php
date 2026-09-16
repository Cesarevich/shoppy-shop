<?php

declare(strict_types=1);

namespace App\Controller\Type;

use App\Catalog\Type\Application\Create\CreateTypeCommand;
use App\Catalog\Type\Domain\TypeAlreadyExists;
use App\Catalog\Type\Domain\TypeCodeAlreadyExists;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\ValueObject\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class TypePutController
{
    public function __construct(private readonly CommandBus $commandBus) {}

    #[Route('/types/{id}', name: 'type_put', methods: ['PUT'], requirements: ['id' => Uuid::PATTERN])]
    public function __invoke(string $id, Request $request): Response
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->toArray();

        try {
            $this->commandBus->dispatch(
                new CreateTypeCommand(
                    $id,
                    self::stringFrom($payload, 'code'),
                    self::stringFrom($payload, 'title'),
                ),
            );
        } catch (TypeAlreadyExists) {
            return new JsonResponse(['error' => 'type_already_exists'], Response::HTTP_CONFLICT);
        } catch (TypeCodeAlreadyExists) {
            return new JsonResponse(['error' => 'type_code_already_exists'], Response::HTTP_CONFLICT);
        }

        return new Response('', Response::HTTP_CREATED);
    }

    /** @param array<string, mixed> $payload */
    private static function stringFrom(array $payload, string $key, string $default = ''): string
    {
        $value = $payload[$key] ?? $default;

        return is_string($value) ? $value : $default;
    }
}
