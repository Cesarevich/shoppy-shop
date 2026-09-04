<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Event;

interface DomainEventSubscriber
{
    /** @return list<class-string<DomainEvent>> */
    public static function subscribedTo(): array;
}
