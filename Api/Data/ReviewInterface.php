<?php

declare(strict_types=1);

namespace CustomerFeel\ApiConnector\Api\Data;

interface ReviewInterface
{
    public function getContent(): string;
    public function setContent(string $content): self;

    public function getSentiment(): ?int;
    public function setSentiment(?int $sentiment): self;

    public function getAuthor(): string;
    public function setAuthor(string $author): self;

    public function getExternalId(): ?string;
    public function setExternalId(?string $externalId): self;
}
