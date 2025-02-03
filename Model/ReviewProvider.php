<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model;

use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Sentimo\ReviewAnalysis\Api\ReviewProviderInterface;
use Sentimo\ReviewAnalysis\Model\ResourceModel\ReviewAnalysisSync;
use Sentimo\ReviewAnalysis\Model\ResourceModel\ReviewAnalysisSync\CollectionFactory as ReviewAnalysisSyncCollectionFactory;
use Sentimo\ReviewAnalysis\Model\ReviewAnalysisSync as ReviewAnalysisSyncModel;

class ReviewProvider implements ReviewProviderInterface
{
    private ?array $completeReviewIds = null;

    /**
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory
     * @param \Sentimo\ReviewAnalysis\Model\ResourceModel\ReviewAnalysisSync\CollectionFactory $reviewAnalysisCollectionFactory
     * @param \Sentimo\ReviewAnalysis\Model\Config $config
     */
    public function __construct(
        private readonly Collectionfactory $reviewCollectionFactory,
        private readonly ReviewAnalysisSyncCollectionFactory $reviewAnalysisCollectionFactory,
        private readonly Config $config
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getNotSyncedReviews(): array
    {
        $fromDate = $this->config->getFromDate();
        $toDate = $this->config->getToDate();

        $collection = $this->reviewCollectionFactory->create();
        $syncTable = $collection->getTable(ReviewAnalysisSync::TABLE_NAME);

        $collection->getSelect()->joinLeft(
            [$syncTable => $syncTable],
            'main_table.review_id = ' . $syncTable . '.review_id',
            ['sentimo_review_analysis_status' => 'status']
        )->where($syncTable . '.review_id IS NULL');

        if ($fromDate !== null) {
            $collection->getSelect()->where('main_table.created_at >= ?', $fromDate);
        }

        if ($toDate !== null) {
            $collection->getSelect()->where('main_table.created_at <= ?', $toDate);
        }

        return $collection->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getSyncInProgressReviewIds(): array
    {
        $collection = $this->reviewAnalysisCollectionFactory->create()
            ->addFieldToSelect('review_id')
            ->addFieldToFilter('status', ReviewAnalysisSyncModel::STATUS_IN_PROGRESS);

        return $collection->getColumnValues('review_id');
    }

    /**
     * @inheritDoc
     */
    public function getReviewAnalysisSyncCompleteReviewIds(): array
    {
        if ($this->completeReviewIds === null) {
            $this->completeReviewIds = $this->reviewAnalysisCollectionFactory->create()
                ->addFieldToFilter('status', ReviewAnalysisSyncModel::STATUS_COMPLETE)
                ->getAllIds();
        }

        return $this->completeReviewIds;
    }
}
