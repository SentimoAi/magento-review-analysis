<?php

declare(strict_types=1);

namespace CustomerFeel\ApiConnector\Model;

use CustomerFeel\ApiConnector\Api\Data\ReviewInterface;
use CustomerFeel\ApiConnector\Api\Data\ReviewInterfaceFactory;
use Magento\Review\Model\Review;

class ReviewAdapter
{
    public function __construct(
        private readonly ReviewInterfaceFactory $reviewFactory
    ) {
    }

    public function adaptTo(Review $review): ReviewInterface
    {
        /** @var ReviewInterface $adapteeReview */
        $adapteeReview = $this->reviewFactory->create();

        return $adapteeReview->setContent($review->getDetail())
            ->setAuthor($review->getNickname())
            ->setExternalId($review->getReviewId());
    }

    public function adaptFrom(array $review): ReviewInterface
    {
        /** @var ReviewInterface $adapteeReview */
        $adapteeReview = $this->reviewFactory->create();

        return $adapteeReview->setContent($review['content'])
            ->setAuthor($review['author'])
            ->setExternalId($review['externalId']);
    }
}
