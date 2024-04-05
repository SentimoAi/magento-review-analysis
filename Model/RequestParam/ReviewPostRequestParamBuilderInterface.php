<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\RequestParam;

use Sentimo\ReviewAnalysis\Api\Data\SentimoReviewInterface;

interface ReviewPostRequestParamBuilderInterface
{
    /**
     * @param \Sentimo\ReviewAnalysis\Api\Data\SentimoReviewInterface $review
     *
     * @return array<string, string|string[]>
     */
    public function buildRequestParam(SentimoReviewInterface $review): array;
}
