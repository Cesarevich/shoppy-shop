<?php

declare(strict_types=1);

namespace App\Tests\Unit\Catalog\ProductHistory\Application\Record\Subscriber;

use App\Catalog\Product\Domain\Event\ProductChangedDomainEvent;
use App\Catalog\ProductHistory\Application\Record\ProductHistoryRecorder;
use App\Catalog\ProductHistory\Application\Record\Subscriber\RecordProductHistoryOnProductChanged;
use App\Catalog\ProductHistory\Infrastructure\Persistence\InMemoryProductHistoryRepository;
use App\Shared\Domain\ValueObject\SimpleUuid;
use PHPUnit\Framework\TestCase;

final class RecordProductHistoryOnProductChangedTest extends TestCase
{
    public function testRecordsFieldChanges(): void
    {
        $repository = new InMemoryProductHistoryRepository();
        $subscriber = new RecordProductHistoryOnProductChanged(new ProductHistoryRecorder($repository));
        $productId = SimpleUuid::random()->value();
        $changes = [
            'title' => [
                'old' => 'Старое название',
                'new' => 'Новое название',
            ],
        ];
        $event = new ProductChangedDomainEvent($productId, $changes);

        $subscriber($event);

        $history = $repository->search($event->eventId());
        self::assertNotNull($history);
        self::assertSame($productId, $history->productId()->value());
        self::assertSame('product.changed', $history->eventName());
        self::assertSame($event->occurredOn(), $history->occurredOn());
        self::assertSame($changes, $history->changes());
    }
}
