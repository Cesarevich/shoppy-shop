<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Application\Record\Subscriber;

use App\Catalog\Product\Domain\Event\ProductRelistedDomainEvent;
use App\Catalog\ProductHistory\Application\Record\ProductHistoryRecorder;
use App\Shared\Domain\Bus\Event\DomainEventSubscriber;

final readonly class RecordProductHistoryOnProductRelisted implements DomainEventSubscriber
{
    public function __construct(private ProductHistoryRecorder $recorder) {}

    public static function subscribedTo(): array
    {
        return [ProductRelistedDomainEvent::class];
    }

    public function __invoke(ProductRelistedDomainEvent $event): void
    {
        $this->recorder->recordRelisted($event);
    }
}
