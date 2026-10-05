<?php

declare(strict_types=1);

namespace App\Catalog\ProductHistory\Application\Find;

use App\Catalog\ProductHistory\Domain\ProductHistories;
use App\Catalog\ProductHistory\Domain\ProductHistory;
use App\Shared\Domain\Bus\Query\Response;

final readonly class ProductHistoriesResponse implements Response
{
    /** @var list<ProductHistoryResponse> */
    private array $histories;

    public function __construct(ProductHistoryResponse ...$histories)
    {
        $this->histories = array_values($histories);
    }

    public static function fromProductHistories(ProductHistories $histories): self
    {
        $responses = [];
        foreach ($histories as $history) {
            if (!$history instanceof ProductHistory) {
                continue;
            }

            $responses[] = ProductHistoryResponse::fromProductHistory($history);
        }

        return new self(...$responses);
    }

    /** @return list<array<string, mixed>> */
    public function toArray(): array
    {
        $rows = [];
        foreach ($this->histories as $history) {
            $rows[] = $history->toArray();
        }

        return $rows;
    }
}
