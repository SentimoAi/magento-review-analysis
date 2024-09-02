<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model;

use Magento\Review\Model\ResourceModel\Review as ReviewResource;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;

class ReviewStatusHandler
{
    /**
     * @param \Magento\Review\Model\ResourceModel\Review $reviewResource
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     */
    public function __construct(
        private readonly ReviewResource $reviewResource,
        private readonly ReviewFactory $reviewFactory
    ) {
    }

    /**
     * @param \Sentimo\Client\Api\Data\Review[] $sentimoReviews
     *
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function updateStatuses(array $sentimoReviews): void
    {
        foreach ($sentimoReviews as $sentimoReview) {
            $review = $this->reviewFactory->create();
            $this->reviewResource->load($review, $sentimoReview->getExternalId()); //phpcs:ignore

            $review->setStatusId($this->mapStatusToMagento($sentimoReview->getModerationStatus()));

            $this->reviewResource->save($review); //phpcs:ignore
            $review->aggregate();
        }
    }

    /**
     * @param string $sentimoStatus
     *
     * @return int
     */
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
