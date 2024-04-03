<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model;

use Magento\Framework\App\ResourceConnection;

class ReviewResource
{
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_SYNCED = 'synced';

    public const TABLE_NAME = 'sentimo_review_analysis_sync';

    public function __construct(
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    /**
     * @param int[] $reviewIds
     * @param string $status
     *
     * @return void
     */
    public function updateReviewsStatus(array $reviewIds, string $status): void
    {
        $data = [];

        foreach ($reviewIds as $reviewId) {
            $data[] = ['review_id' => $reviewId, 'status' => $status];
        }

        $connection = $this->resourceConnection->getConnection();
        $connection->insertOnDuplicate(
            $this->resourceConnection->getTableName(self::TABLE_NAME),
            $data
        );
    }

    //Todo implement
    public function updateReviewsSentiments(array $reviewIds, string $sentiment): void
    {
//        $data = [];
//        foreach ($reviewIds as $reviewId) {
//            $data[] = ['review_id' => $reviewId, 'status' => $status];
//        }
//
//        $connection = $this->resourceConnection->getConnection();
//        $connection->insertOnDuplicate(
//            $this->resourceConnection->getTableName(self::TABLE_NAME),
//            $data
//        );
    }
}
