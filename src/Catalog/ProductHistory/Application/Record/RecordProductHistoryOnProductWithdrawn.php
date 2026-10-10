<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Application\Record;

use App\Catalog\Product\Domain\Event\ProductWithdrawnDomainEvent;
use App\Shared\Domain\Bus\Event\DomainEventSubscriber;

final readonly class RecordProductHistoryOnProductWithdrawn implements DomainEventSubscriber
{
    public function __construct(private ProductHistoryRecorder $recorder) {}

    public static function subscribedTo(): array
    {
        return [ProductWithdrawnDomainEvent::class];
    }

    public function __invoke(ProductWithdrawnDomainEvent $event): void
    {
        $this->recorder->recordWithdrawn($event);
    }
}
