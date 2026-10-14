<?php

declare(strict_types=1);

namespace App\Tests\Unit\Catalog\ProductHistory\Application\Find;

use App\Catalog\Product\Domain\ValueObject\ProductId;
use App\Catalog\ProductHistory\Application\Find\FindProductHistoryQuery;
use App\Catalog\ProductHistory\Application\Find\FindProductHistoryQueryHandler;
use App\Catalog\ProductHistory\Application\Find\ProductHistoryFinder;
use App\Catalog\ProductHistory\Domain\ProductHistory;
use App\Catalog\ProductHistory\Infrastructure\Persistence\InMemoryProductHistoryRepository;
use PHPUnit\Framework\TestCase;

final class FindProductHistoryQueryHandlerTest extends TestCase
{
    public function testReturnsEmptyListWhenProductHasNoHistory(): void
    {
        $handler = $this->handler(new InMemoryProductHistoryRepository());

        $response = $handler->__invoke(new FindProductHistoryQuery(ProductId::random()));

        self::assertSame([], $response->toArray());
    }

    public function testReturnsEmptyListForUnknownProduct(): void
    {
        $repository = new InMemoryProductHistoryRepository();
        $repository->save($this->history(ProductId::random(), '2026-01-01T00:00:00+00:00', 'product.listed'));
        $handler = $this->handler($repository);

        $response = $handler->__invoke(new FindProductHistoryQuery(ProductId::random()));

        self::assertSame([], $response->toArray());
    }

    public function testReturnsRowsInTimeOrder(): void
    {
        $productId = ProductId::random();
        $repository = new InMemoryProductHistoryRepository();
        $repository->save($this->history($productId, '2026-03-01T00:00:00+00:00', 'product.relisted'));
        $repository->save($this->history($productId, '2026-01-01T00:00:00+00:00', 'product.listed'));
        $repository->save($this->history($productId, '2026-02-01T00:00:00+00:00', 'product.withdrawn'));
        $handler = $this->handler($repository);

        $response = $handler->__invoke(new FindProductHistoryQuery($productId));

        self::assertSame(
            ['product.listed', 'product.withdrawn', 'product.relisted'],
            array_column($response->toArray(), 'eventName'),
        );
    }

    private function handler(InMemoryProductHistoryRepository $repository): FindProductHistoryQueryHandler
    {
        return new FindProductHistoryQueryHandler(new ProductHistoryFinder($repository));
    }

    private function history(ProductId $productId, string $occurredOn, string $eventName): ProductHistory
    {
        return ProductHistory::create(
            $productId->value() . '-' . $eventName,
            $productId,
            $eventName,
            $occurredOn,
            [],
        );
    }
}
