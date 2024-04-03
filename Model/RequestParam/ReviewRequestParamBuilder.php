<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\RequestParam;

use Sentimo\ReviewAnalysis\Api\Data\ReviewInterface;
use Sentimo\ReviewAnalysis\Model\Config;

class ReviewRequestParamBuilder implements ReviewRequestParamBuilderInterface
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * @inheritDoc
     */
    public function buildRequestParam(ReviewInterface $review): array
    {
        return [
            'content' => $review->getContent(),
            'externalId' => $review->getExternalId() ?? '',
            'channel' => $this->config->getChannel() ?? '',
            'rating' => $review->getRating() ?? '',
        ];
    }
}
