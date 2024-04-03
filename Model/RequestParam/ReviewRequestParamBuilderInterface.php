<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\RequestParam;

use Sentimo\ReviewAnalysis\Api\Data\ReviewInterface;

interface ReviewRequestParamBuilderInterface
{
    /**
     * @param \Sentimo\ReviewAnalysis\Api\Data\ReviewInterface $review
     *
     * @return array<string, string|string[]>
     */
    public function buildRequestParam(ReviewInterface $review): array;
}
