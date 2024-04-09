<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\Adapter;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory as VoteCollectionFactory;
use Magento\Review\Model\Review;
use Sentimo\ReviewAnalysis\Api\Data\AuthorInterfaceFactory;
use Sentimo\ReviewAnalysis\Api\Data\SentimoReviewInterface;
use Sentimo\ReviewAnalysis\Api\Data\SentimoReviewInterfaceFactory;

class ReviewAdapter
{
    /**
     * @param \Sentimo\ReviewAnalysis\Api\Data\SentimoReviewInterfaceFactory $sentimoReviewFactory
     * @param \Sentimo\ReviewAnalysis\Api\Data\AuthorInterfaceFactory $authorFactory
     * @param \Sentimo\ReviewAnalysis\Model\Adapter\ProductAdapter $productAdapter
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $voteCollectionFactory
     */
    public function __construct(
        private readonly SentimoReviewInterfaceFactory $sentimoReviewFactory,
        private readonly AuthorInterfaceFactory $authorFactory,
        private readonly ProductAdapter $productAdapter,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly VoteCollectionFactory $voteCollectionFactory,
    ) {
    }

    /**
     * @param \Magento\Review\Model\Review $review
     *
     * @return \Sentimo\ReviewAnalysis\Api\Data\SentimoReviewInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function adaptTo(Review $review): SentimoReviewInterface
    {
        /** @var \Sentimo\ReviewAnalysis\Api\Data\SentimoReviewInterface $adapteeReview */
        $adapteeReview = $this->sentimoReviewFactory->create();

        $adapteeReview->setContent($review->getDetail())
            ->setRating($this->getRatingValue($review))
            ->setAuthor($this->authorFactory->create([
                'nickname' => $review->getNickname(),
                'external_id' => $review->getCustomerId(),
            ]))
            ->setExternalId($review->getReviewId());

        $product = $this->getProductByReview($review);

        if ($product !== null) {
            $adapteeReview->setProduct($this->productAdapter->adaptTo($product));
        }

        return $adapteeReview;
    }

    private function getProductByReview(Review $review): ?ProductInterface
    {
        try {
            return $this->productRepository->getById((int) $review->getEntityPkValue());
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    private function getRatingValue(Review $review): ?int
    {
        $voteCollection = $this->voteCollectionFactory->create();

        $rating = $voteCollection
            ->setReviewFilter($review->getId())
            ->getFirstItem();

        if ($rating->getId() === null) {
            return null;
        }

        return (int) $rating->getValue();
    }
}
