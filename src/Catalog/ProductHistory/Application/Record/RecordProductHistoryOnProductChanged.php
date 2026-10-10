<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Application\Record;

use App\Catalog\Product\Domain\Event\ProductChangedDomainEvent;
use App\Shared\Domain\Bus\Event\DomainEventSubscriber;

final readonly class RecordProductHistoryOnProductChanged implements DomainEventSubscriber
{
    public function __construct(private ProductHistoryRecorder $recorder) {}

    public static function subscribedTo(): array
    {
        return [ProductChangedDomainEvent::class];
    }

    public function __invoke(ProductChangedDomainEvent $event): void
    {
        $this->recorder->recordChanged($event);
    }
}
