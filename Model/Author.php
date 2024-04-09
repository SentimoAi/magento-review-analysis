<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model;

use Sentimo\ReviewAnalysis\Api\Data\AuthorInterface;

class Author implements AuthorInterface
{
    public function __construct(
        private readonly string $nickname,
        private readonly ?string $externalId = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getNickname(): string
    {
         return $this->nickname;
    }

    /**
     * @inheritDoc
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
}
