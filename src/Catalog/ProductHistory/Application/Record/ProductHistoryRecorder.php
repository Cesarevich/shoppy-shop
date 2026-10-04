<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Application\Record;

use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\Product\Domain\ProductChangedDomainEvent;
use App\Catalog\Product\Domain\ProductId;
use App\Catalog\Product\Domain\ProductListedDomainEvent;
use App\Catalog\Product\Domain\ProductRelistedDomainEvent;
use App\Catalog\Product\Domain\ProductWithdrawnDomainEvent;
use App\Catalog\ProductHistory\Domain\ProductHistory;
use App\Catalog\ProductHistory\Domain\ProductHistoryRepository;

final readonly class ProductHistoryRecorder
{
    public function __construct(private ProductHistoryRepository $repository) {}

    public function recordChanged(ProductChangedDomainEvent $event): void
    {
        if (null !== $this->repository->search($event->eventId())) {
            return;
        }

        $this->repository->save(ProductHistory::create(
            $event->eventId(),
            new ProductId($event->aggregateId()),
            $event::eventName(),
            $event->occurredOn(),
            $event->changes(),
        ));
    }

    public function recordListed(ProductListedDomainEvent $event): void
    {
        if (null !== $this->repository->search($event->eventId())) {
            return;
        }

        $this->repository->save(ProductHistory::create(
            $event->eventId(),
            new ProductId($event->aggregateId()),
            $event::eventName(),
            $event->occurredOn(),
            [
                'listingStatus' => [
                    'old' => ListingStatus::Draft->value,
                    'new' => ListingStatus::OnSale->value,
                ],
            ],
        ));
    }

    public function recordRelisted(ProductRelistedDomainEvent $event): void
    {
        if (null !== $this->repository->search($event->eventId())) {
            return;
        }

        $this->repository->save(ProductHistory::create(
            $event->eventId(),
            new ProductId($event->aggregateId()),
            $event::eventName(),
            $event->occurredOn(),
            [
                'listingStatus' => [
                    'old' => ListingStatus::Withdrawn->value,
                    'new' => ListingStatus::OnSale->value,
                ],
            ],
        ));
    }

    public function recordWithdrawn(ProductWithdrawnDomainEvent $event): void
    {
        if (null !== $this->repository->search($event->eventId())) {
            return;
        }

        $this->repository->save(ProductHistory::create(
            $event->eventId(),
            new ProductId($event->aggregateId()),
            $event::eventName(),
            $event->occurredOn(),
            [
                'listingStatus' => [
                    'old' => ListingStatus::OnSale->value,
                    'new' => ListingStatus::Withdrawn->value,
                ],
            ],
        ));
    }
}
