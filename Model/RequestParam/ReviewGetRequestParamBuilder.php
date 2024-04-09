<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\RequestParam;

use Sentimo\ReviewAnalysis\Api\ReviewProviderInterface;

class ReviewGetRequestParamBuilder implements ReviewGetRequestParamBuilderInterface
{
    /**
     * @param \Sentimo\ReviewAnalysis\Api\ReviewProviderInterface $reviewProvider
     */
    public function __construct(
        private readonly ReviewProviderInterface $reviewProvider
    ) {
    }

    /**
     * @inheritDoc
     */
    public function buildRequestParam(): array
    {
        return [
            'externalId' => $this->reviewProvider->getSyncInProgressReviewIds(),
            'exists' => ['moderationStatus' => true],
        ];
    }
}
