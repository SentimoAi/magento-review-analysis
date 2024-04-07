<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ReviewAnalysisSync extends AbstractDb
{
    public const TABLE_NAME = 'sentimo_review_analysis_sync';

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
            if ($reviewId === null) {
                continue;
            }

            $data[] = ['review_id' => $reviewId, 'status' => $status];
        }

        $connection = $this->getConnection();
        $connection->insertOnDuplicate(
            $this->getMainTable(),
            $data
        );
    }

    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, 'review_id');
    }
}
