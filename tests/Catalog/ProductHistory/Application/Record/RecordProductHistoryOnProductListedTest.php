<?php

declare(strict_types=1);

namespace App\Tests\Catalog\ProductHistory\Application\Record;

use App\Catalog\Product\Domain\Event\ProductListedDomainEvent;
use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\ProductHistory\Application\Record\ProductHistoryRecorder;
use App\Catalog\ProductHistory\Application\Record\RecordProductHistoryOnProductListed;
use App\Catalog\ProductHistory\Infrastructure\Persistence\InMemoryProductHistoryRepository;
use App\Shared\Domain\ValueObject\SimpleUuid;
use PHPUnit\Framework\TestCase;

final class RecordProductHistoryOnProductListedTest extends TestCase
{
    public function testRecordsDraftToOnSale(): void
    {
        $repository = new InMemoryProductHistoryRepository();
        $subscriber = new RecordProductHistoryOnProductListed(new ProductHistoryRecorder($repository));
        $productId = SimpleUuid::random()->value();
        $event = new ProductListedDomainEvent($productId);

        $subscriber($event);

        $history = $repository->search($event->eventId());
        self::assertNotNull($history);
        self::assertSame($productId, $history->productId()->value());
        self::assertSame('product.listed', $history->eventName());
        self::assertSame($event->occurredOn(), $history->occurredOn());
        self::assertSame([
            'listingStatus' => [
                'old' => ListingStatus::Draft->value,
                'new' => ListingStatus::OnSale->value,
            ],
        ], $history->changes());
    }

    public function testSkipsTheSameEventTwice(): void
    {
        $repository = new InMemoryProductHistoryRepository();
        $subscriber = new RecordProductHistoryOnProductListed(new ProductHistoryRecorder($repository));
        $event = new ProductListedDomainEvent(SimpleUuid::random()->value(), '6f1c3a2e-4b5d-4e6f-8a9b-0c1d2e3f4a5b');

        $subscriber($event);
        $subscriber($event);

        $history = $repository->search($event->eventId());
        self::assertNotNull($history);
        self::assertSame([
            'listingStatus' => [
                'old' => ListingStatus::Draft->value,
                'new' => ListingStatus::OnSale->value,
            ],
        ], $history->changes());
    }
}
