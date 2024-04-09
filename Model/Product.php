<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model;

use Sentimo\ReviewAnalysis\Api\Data\ProductInterface;

class Product implements ProductInterface
{
    /**
     * @param string $name
     * @param string|null $description
     * @param string|null $price
     * @param string|null $identifier
     * @param string|null $productType
     */
    public function __construct(
        private readonly string $name,
        private readonly ?string $description = null,
        private readonly ?string $price = null,
        private readonly ?string $identifier = null,
        private readonly ?string $productType = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function getProductType(): ?string
    {
        return $this->productType;
    }
}
