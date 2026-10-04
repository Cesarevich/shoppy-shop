<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Application\Record;

use App\Catalog\Product\Domain\ProductListedDomainEvent;
use App\Shared\Domain\Bus\Event\DomainEventSubscriber;

final readonly class RecordProductHistoryOnProductListed implements DomainEventSubscriber
{
    public function __construct(private ProductHistoryRecorder $recorder) {}

    public static function subscribedTo(): array
    {
        return [ProductListedDomainEvent::class];
    }

    public function __invoke(ProductListedDomainEvent $event): void
    {
        $this->recorder->recordListed($event);
    }
}
