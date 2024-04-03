<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Api\Data;

interface ProductInterface
{
    public function getName(): string;

    public function getDescription(): ?string;

    public function getPrice(): ?string;

    public function getIdentifier(): ?string;

    public function getProductType(): ?string;
}
