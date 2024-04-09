<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Api\Data;

interface AuthorInterface
{
    /**
     * @return string
     */
    public function getNickname(): string;

    /**
     * @return string|null
     */
    public function getExternalId(): ?string;
}
