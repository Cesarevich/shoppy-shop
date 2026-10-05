<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Application\Find;

use App\Catalog\ProductHistory\Domain\ProductHistory;
use App\Shared\Domain\Bus\Query\Response;

final readonly class ProductHistoryResponse implements Response
{
    /**
     * @param array<string, array{old: mixed, new: mixed}> $changes
     */
    public function __construct(
        public string $id,
        public string $productId,
        public string $eventName,
        public string $occurredOn,
        public array $changes,
    ) {}

    public static function fromProductHistory(ProductHistory $history): self
    {
        return new self(
            $history->id(),
            $history->productId()->value(),
            $history->eventName(),
            $history->occurredOn(),
            $history->changes(),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'productId' => $this->productId,
            'eventName' => $this->eventName,
            'occurredOn' => $this->occurredOn,
            'changes' => $this->changes,
        ];
    }
}
