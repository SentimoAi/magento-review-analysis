<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Api\Data;

interface SentimoReviewInterface
{
    public function getContent(): string;

    public function setContent(string $content): self;

    public function getModerationStatus(): ?string;

    public function setModerationStatus(string $moderationStatus): self;

    public function getAuthor(): AuthorInterface;

    public function setAuthor(AuthorInterface $author): self;

    public function getExternalId(): ?string;

    public function setExternalId(string $externalId): self;

    public function setProduct(ProductInterface $product): self;

    public function getProduct(): ?ProductInterface;

    public function getRating(): ?int;

    public function setRating(?int $rating): self;
}
