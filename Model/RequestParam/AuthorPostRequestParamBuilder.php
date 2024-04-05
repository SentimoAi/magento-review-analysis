<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\RequestParam;

use Sentimo\ReviewAnalysis\Api\Data\SentimoReviewInterface;

class AuthorPostRequestParamBuilder implements ReviewPostRequestParamBuilderInterface
{
    /**
     * @inheritDoc
     */
    public function buildRequestParam(SentimoReviewInterface $review): array
    {
        return [
            'author' => [
                'nickname' => $review->getAuthor()->getNickname(),
                'externalId' => $review->getAuthor()->getExternalId() ?? '',
            ],
        ];
    }
}
