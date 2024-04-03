<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model;

use Magento\Review\Model\ResourceModel\Review\CollectionFactory;

class ReviewProvider
{
    public function __construct(
        private readonly Collectionfactory $reviewCollectionFactory,
        private readonly Config $config
    ) {
    }

    /**
     * Todo : filter collection
     * Todo :  interface
     *
     * @return \Magento\Review\Model\Review[]
     */
    public function getReviewsToSync(): array
    {
        $fromDate = $this->config->getFromDate();
        $toDate = $this->config->getToDate();

        $collection = $this->reviewCollectionFactory->create();
        $statusTable = $collection->getTable(ReviewResource::TABLE_NAME);

        $collection->getSelect()->joinLeft(
            [$statusTable => $statusTable],
            'main_table.review_id = ' . $statusTable . '.review_id',
            ['sentimo_review_analysis_status' => 'status']
        )->where($statusTable . '.review_id IS NULL');

        if ($fromDate !== null) {
            $collection->getSelect()->where('main_table.created_at >= ?', $fromDate);
        }

        if ($toDate !== null) {
            $collection->getSelect()->where('main_table.created_at <= ?', $toDate);
        }

        return $collection->getItems();
    }
}
