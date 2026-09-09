<?php

declare(strict_types=1);

namespace App\Catalog\Product\Application\Create;

use App\Shared\Domain\Bus\Command\Command;

final readonly class CreateProductCommand implements Command
{
    public function __construct(
        private string $id,
        private string $typeId,
        private string $title,
        private ?string $ean,
        private ?string $description,
        private ?int $year,
        private ?int $weight,
        private ?int $length,
        private ?int $width,
        private ?int $height,
        private string $listingStatus,
        private int $listPriceAmount,
        private string $listPriceCurrency,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function typeId(): string
    {
        return $this->typeId;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function ean(): ?string
    {
        return $this->ean;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function year(): ?int
    {
        return $this->year;
    }

    public function weight(): ?int
    {
        return $this->weight;
    }

    public function length(): ?int
    {
        return $this->length;
    }

    public function width(): ?int
    {
        return $this->width;
    }

    public function height(): ?int
    {
        return $this->height;
    }

    public function listingStatus(): string
    {
        return $this->listingStatus;
    }

    public function listPriceAmount(): int
    {
        return $this->listPriceAmount;
    }

    public function listPriceCurrency(): string
    {
        return $this->listPriceCurrency;
    }
}
