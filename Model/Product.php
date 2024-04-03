<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model;

use Sentimo\ReviewAnalysis\Api\Data\ProductInterface;

class Product implements ProductInterface
{
    public function __construct(
        private readonly string $name,
        private readonly ?string $description = null,
        private readonly ?string $price = null,
        private readonly ?string $identifier = null,
        private readonly ?string $productType = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getProductType(): ?string
    {
        return $this->productType;
    }
}
