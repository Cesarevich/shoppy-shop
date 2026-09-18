<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Domain;

use App\Catalog\Product\Domain\ListingStatus;
use App\Catalog\Product\Domain\ProductAlreadyOnSale;
use App\Catalog\Product\Domain\ProductAlreadyWithdrawn;
use App\Catalog\Product\Domain\ProductListedDomainEvent;
use App\Catalog\Product\Domain\ProductNotInDraft;
use App\Catalog\Product\Domain\ProductNotOnSale;
use App\Catalog\Product\Domain\ProductNotWithdrawn;
use App\Catalog\Product\Domain\ProductRelistedDomainEvent;
use App\Catalog\Product\Domain\ProductWithdrawnDomainEvent;
use PHPUnit\Framework\TestCase;

final class ProductListingTest extends TestCase
{
    public function testListsDraftProduct(): void
    {
        $product = ProductMother::create(listingStatus: ListingStatus::Draft);

        $product->listOnSale();

        self::assertSame(ListingStatus::OnSale, $product->listingStatus());
        $events = $product->pullDomainEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(ProductListedDomainEvent::class, $events[0]);
        self::assertSame('product.listed', $events[0]::eventName());
        self::assertSame($product->id()->value(), $events[0]->aggregateId());
    }

    public function testRejectsListingWhenAlreadyOnSale(): void
    {
        $product = ProductMother::create(listingStatus: ListingStatus::OnSale);

        $this->expectException(ProductAlreadyOnSale::class);
        $product->listOnSale();
    }

    public function testRejectsListingWhenWithdrawn(): void
    {
        $product = ProductMother::create(listingStatus: ListingStatus::Withdrawn);

        $this->expectException(ProductNotInDraft::class);
        $product->listOnSale();
    }

    public function testWithdrawsProductOnSale(): void
    {
        $product = ProductMother::create(listingStatus: ListingStatus::OnSale);

        $product->withdraw();

        self::assertSame(ListingStatus::Withdrawn, $product->listingStatus());
        $events = $product->pullDomainEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(ProductWithdrawnDomainEvent::class, $events[0]);
        self::assertSame('product.withdrawn', $events[0]::eventName());
        self::assertSame($product->id()->value(), $events[0]->aggregateId());
    }

    public function testRejectsWithdrawWhenAlreadyWithdrawn(): void
    {
        $product = ProductMother::create(listingStatus: ListingStatus::Withdrawn);

        $this->expectException(ProductAlreadyWithdrawn::class);
        $product->withdraw();
    }

    public function testRejectsWithdrawWhenNotOnSale(): void
    {
        $product = ProductMother::create(listingStatus: ListingStatus::Draft);

        $this->expectException(ProductNotOnSale::class);
        $product->withdraw();
    }

    public function testRelistsWithdrawnProduct(): void
    {
        $product = ProductMother::create(listingStatus: ListingStatus::Withdrawn);

        $product->relistOnSale();

        self::assertSame(ListingStatus::OnSale, $product->listingStatus());
        $events = $product->pullDomainEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(ProductRelistedDomainEvent::class, $events[0]);
        self::assertSame('product.relisted', $events[0]::eventName());
        self::assertSame($product->id()->value(), $events[0]->aggregateId());
    }

    public function testRejectsRelistWhenAlreadyOnSale(): void
    {
        $product = ProductMother::create(listingStatus: ListingStatus::OnSale);

        $this->expectException(ProductAlreadyOnSale::class);
        $product->relistOnSale();
    }

    public function testRejectsRelistWhenNotWithdrawn(): void
    {
        $product = ProductMother::create(listingStatus: ListingStatus::Draft);

        $this->expectException(ProductNotWithdrawn::class);
        $product->relistOnSale();
    }
}
