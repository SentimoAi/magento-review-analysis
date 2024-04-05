<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model;

use Sentimo\ReviewAnalysis\Api\Data\AuthorInterface;
use Sentimo\ReviewAnalysis\Api\Data\ProductInterface;
use Sentimo\ReviewAnalysis\Api\Data\SentimoReviewInterface;

class SentimoReview implements SentimoReviewInterface
{
    private string $content;
    private ?string $moderationStatus = null;
    private AuthorInterface $author;
    private string $externalId;
    private ?ProductInterface $product;
    private ?int $rating;

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): AuthorInterface
    {
        return $this->author;
    }

    public function setAuthor(AuthorInterface $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getModerationStatus(): ?string
    {
        return $this->moderationStatus;
    }

    public function setModerationStatus(string $moderationStatus): SentimoReviewInterface
    {
        $this->moderationStatus = $moderationStatus;

        return $this;
    }

    public function setProduct(ProductInterface $product): SentimoReviewInterface
    {
        $this->product = $product;

        return $this;
    }

    public function getProduct(): ?ProductInterface
    {
        return $this->product;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
