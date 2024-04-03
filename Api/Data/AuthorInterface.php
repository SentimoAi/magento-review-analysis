<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Api\Data;

interface AuthorInterface
{
    public function getNickname(): string;

    public function getExternalId(): ?string;
}
