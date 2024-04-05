<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model;

use Magento\Review\Model\ResourceModel\Review as ReviewResource;
use Magento\Review\Model\Review;

class ReviewStatusHandler
{
    public function __construct(
        private readonly ReviewResource $reviewResource
    ) {
    }

    /**
     * @param array<string, string|int|string[]> $sentimoReviews
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateStatuses(array $sentimoReviews): void
    {
        $connection = $this->reviewResource->getConnection();

        foreach ($sentimoReviews as $review) {
            $bind = ['status_id' => $this->mapStatusToMagento($review['moderationStatus'])];
            $where = ['review_id = ?' => $review['externalId']];

            $connection->update($this->reviewResource->getMainTable(), $bind, $where);
        }
    }

    private function mapStatusToMagento(string $sentimoStatus): int
    {
        $statusMap = [
            'approved' => Review::STATUS_APPROVED,
            'denied' => Review::STATUS_NOT_APPROVED,
            'hold' => Review::STATUS_PENDING,
        ];

        return $statusMap[$sentimoStatus] ?? Review::STATUS_PENDING;
    }
}
