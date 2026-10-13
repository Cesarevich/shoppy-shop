<?php

declare(strict_types=1);

namespace App\Tests\Catalog\ProductHistory\Application\Record\Subscriber;

use App\Catalog\Product\Domain\Event\ProductRelistedDomainEvent;
use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\ProductHistory\Application\Record\ProductHistoryRecorder;
use App\Catalog\ProductHistory\Application\Record\Subscriber\RecordProductHistoryOnProductRelisted;
use App\Catalog\ProductHistory\Infrastructure\Persistence\InMemoryProductHistoryRepository;
use App\Shared\Domain\ValueObject\SimpleUuid;
use PHPUnit\Framework\TestCase;

final class RecordProductHistoryOnProductRelistedTest extends TestCase
{
    public function testRecordsWithdrawnToOnSale(): void
    {
        $repository = new InMemoryProductHistoryRepository();
        $subscriber = new RecordProductHistoryOnProductRelisted(new ProductHistoryRecorder($repository));
        $productId = SimpleUuid::random()->value();
        $event = new ProductRelistedDomainEvent($productId);

        $subscriber($event);

        $history = $repository->search($event->eventId());
        self::assertNotNull($history);
        self::assertSame($productId, $history->productId()->value());
        self::assertSame('product.relisted', $history->eventName());
        self::assertSame($event->occurredOn(), $history->occurredOn());
        self::assertSame([
            'listingStatus' => [
                'old' => ListingStatus::Withdrawn->value,
                'new' => ListingStatus::OnSale->value,
            ],
        ], $history->changes());
    }
}
