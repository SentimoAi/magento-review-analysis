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

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAuthor(): AuthorInterface
    {
        return $this->author;
    }

    /**
     * @inheritDoc
     */
    public function setAuthor(AuthorInterface $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
     * @inheritDoc
     */
    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModerationStatus(): ?string
    {
        return $this->moderationStatus;
    }

    /**
     * @inheritDoc
     */
    public function setModerationStatus(string $moderationStatus): SentimoReviewInterface
    {
        $this->moderationStatus = $moderationStatus;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setProduct(ProductInterface $product): SentimoReviewInterface
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProduct(): ?ProductInterface
    {
        return $this->product;
    }

    /**
     * @inheritDoc
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * @inheritDoc
     */
    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
