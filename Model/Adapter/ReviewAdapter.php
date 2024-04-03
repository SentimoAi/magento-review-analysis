<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\Adapter;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory as VoteCollectionFactory;
use Magento\Review\Model\Review;
use Ramsey\Collection\Exception\NoSuchElementException;
use Sentimo\ReviewAnalysis\Api\Data\AuthorInterfaceFactory;
use Sentimo\ReviewAnalysis\Api\Data\ReviewInterface;
use Sentimo\ReviewAnalysis\Api\Data\ReviewInterfaceFactory;

class ReviewAdapter
{
    private const REVIEW_RATING_ID = 4;

    public function __construct(
        private readonly ReviewInterfaceFactory $reviewFactory,
        private readonly AuthorInterfaceFactory $authorFactory,
        private readonly ProductAdapter $productAdapter,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly VoteCollectionFactory $voteCollectionFactory,
    ) {
    }

    public function adaptTo(Review $review): ReviewInterface
    {
        /** @var \Sentimo\ReviewAnalysis\Api\Data\ReviewInterface $adapteeReview */
        $adapteeReview = $this->reviewFactory->create();

        $adapteeReview->setContent($review->getDetail())
            ->setRating($this->getRatingValue($review))
            ->setAuthor($this->authorFactory->create([
                'nickname' => $review->getNickname(), 'external_id' => $review->getCustomerId(),
            ]))
            ->setExternalId($review->getReviewId());

        $product = $this->getProductByReview($review);

        if ($product !== null) {
            $adapteeReview->setProduct($this->productAdapter->adaptTo($product));
        }

        return $adapteeReview;
    }

    public function adaptFrom(array $review): ReviewInterface
    {
        /** @var \Sentimo\ReviewAnalysis\Api\Data\ReviewInterface $adapteeReview */
        $adapteeReview = $this->reviewFactory->create();

        return $adapteeReview->setContent($review['content'])
            ->setAuthor($review['author'])
            ->setExternalId($review['externalId']);
    }

    public function getProductByReview(Review $review): ?ProductInterface
    {
        try {
            return $this->productRepository->getById((int) $review->getEntityPkValue());
        } catch (NoSuchElementException $exception) {
            return null;
        }
    }

    private function getRatingValue(Review $review): ?int
    {
        $voteCollection = $this->voteCollectionFactory->create();

        $rating = $voteCollection
            ->setReviewFilter($review->getId())
            ->addFieldToFilter('rating_id', self::REVIEW_RATING_ID)
            ->getFirstItem();

        if ($rating->getId() === null) {
            return null;
        }

        return (int) $rating->getValue();
    }
}
