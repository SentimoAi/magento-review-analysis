<?php

declare(strict_types=1);

namespace CustomerFeel\ApiConnector\Model;

use Magento\Review\Model\ResourceModel\Review\CollectionFactory;

class ReviewProvider
{
    public function __construct(private readonly Collectionfactory $reviewCollectionFactory)
    {
    }

    /**
     * Todo : filter collection
     * @return \Magento\Review\Model\Review[]
     */
    public function getReviewsToSync(): array
    {
        $collection = $this->reviewCollectionFactory->create();
        $statusTable = $collection->getTable(ReviewResource::TABLE_NAME);

        $collection->getSelect()->joinLeft(
            $statusTable,
            'main_table.review_id = ' . $statusTable . '.review_id where ' . $statusTable . '.review_id IS NULL',
            ['customerfeel_status' => 'status']
        );

        return $collection->getItems();
    }
}
