<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\RequestParam;

use Sentimo\ReviewAnalysis\Api\Data\ReviewInterface;

class AuthorRequestParamBuilder implements ReviewRequestParamBuilderInterface
{
    /**
     * @inheritDoc
     */
    public function buildRequestParam(ReviewInterface $review): array
    {
        return [
            'author' => [
                'nickname' => $review->getAuthor()->getNickname(),
                'externalId' => $review->getAuthor()->getExternalId() ?? '',
            ],
        ];
    }
}
