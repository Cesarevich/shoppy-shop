<?php

declare(strict_types=1);

namespace App\Tests\Catalog\ProductHistory\Application\Record\Subscriber;

use App\Catalog\Product\Domain\Event\ProductWithdrawnDomainEvent;
use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\ProductHistory\Application\Record\ProductHistoryRecorder;
use App\Catalog\ProductHistory\Application\Record\Subscriber\RecordProductHistoryOnProductWithdrawn;
use App\Catalog\ProductHistory\Infrastructure\Persistence\InMemoryProductHistoryRepository;
use App\Shared\Domain\ValueObject\SimpleUuid;
use PHPUnit\Framework\TestCase;

final class RecordProductHistoryOnProductWithdrawnTest extends TestCase
{
    public function testRecordsOnSaleToWithdrawn(): void
    {
        $repository = new InMemoryProductHistoryRepository();
        $subscriber = new RecordProductHistoryOnProductWithdrawn(new ProductHistoryRecorder($repository));
        $productId = SimpleUuid::random()->value();
        $event = new ProductWithdrawnDomainEvent($productId);

        $subscriber($event);

        $history = $repository->search($event->eventId());
        self::assertNotNull($history);
        self::assertSame($productId, $history->productId()->value());
        self::assertSame('product.withdrawn', $history->eventName());
        self::assertSame($event->occurredOn(), $history->occurredOn());
        self::assertSame([
            'listingStatus' => [
                'old' => ListingStatus::OnSale->value,
                'new' => ListingStatus::Withdrawn->value,
            ],
        ], $history->changes());
    }
}
