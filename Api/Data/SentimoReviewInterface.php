<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Api\Data;

interface SentimoReviewInterface
{
    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @param string $content
     *
     * @return self
     */
    public function setContent(string $content): self;

    /**
     * @return string|null
     */
    public function getModerationStatus(): ?string;

    /**
     * @param string $moderationStatus
     *
     * @return self
     */
    public function setModerationStatus(string $moderationStatus): self;

    /**
     * @return \Sentimo\ReviewAnalysis\Api\Data\AuthorInterface
     */
    public function getAuthor(): AuthorInterface;

    /**
     * @param \Sentimo\ReviewAnalysis\Api\Data\AuthorInterface $author
     *
     * @return self
     */
    public function setAuthor(AuthorInterface $author): self;

    /**
     * @return string|null
     */
    public function getExternalId(): ?string;

    /**
     * @param string $externalId
     *
     * @return self
     */
    public function setExternalId(string $externalId): self;

    /**
     * @param \Sentimo\ReviewAnalysis\Api\Data\ProductInterface $product
     *
     * @return self
     */
    public function setProduct(ProductInterface $product): self;

    /**
     * @return \Sentimo\ReviewAnalysis\Api\Data\ProductInterface|null
     */
    public function getProduct(): ?ProductInterface;

    /**
     * @return int|null
     */
    public function getRating(): ?int;

    /**
     * @param int|null $rating
     *
     * @return self
     */
    public function setRating(?int $rating): self;
}
