<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Api;

interface ReviewProviderInterface
{
    /**
     * @return \Magento\Review\Model\Review[]
     */
    public function getNotSyncedReviews(): array;

    /**
     * @return int[]
     */
    public function getSyncInProgressReviewIds(): array;
}
