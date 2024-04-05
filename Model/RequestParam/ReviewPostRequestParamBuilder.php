<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\RequestParam;

use Sentimo\ReviewAnalysis\Api\Data\SentimoReviewInterface;
use Sentimo\ReviewAnalysis\Model\Config;

class ReviewPostRequestParamBuilder implements ReviewPostRequestParamBuilderInterface
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * @inheritDoc
     */
    public function buildRequestParam(SentimoReviewInterface $review): array
    {
        return [
            'content' => $review->getContent(),
            'externalId' => $review->getExternalId() ?? '',
            'channel' => $this->config->getChannel() ?? '',
            'rating' => $review->getRating() ?? '',
        ];
    }
}
