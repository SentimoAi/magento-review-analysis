<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\Adapter;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory as VoteCollectionFactory;
use Magento\Review\Model\Review;
use Sentimo\Client\Api\Data\ReviewInterface;
use Sentimo\Client\Api\Data\AuthorFactory;
use Sentimo\Client\Api\Data\ReviewFactory;

class ReviewAdapter
{
    /**
     * @param ReviewFactory $sentimoReviewFactory
     * @param AuthorFactory $authorFactory
     * @param \Sentimo\ReviewAnalysis\Model\Adapter\ProductAdapter $productAdapter
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $voteCollectionFactory
     */
    public function __construct(
        private readonly ReviewFactory $sentimoReviewFactory,
        private readonly AuthorFactory $authorFactory,
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
    public function adaptTo(Review $review): ReviewInterface
    {

        $reviewData = [
            'content' => $review->getDetail(),
            'author' => $this->authorFactory->create([
                'nickName' => $review->getNickname(),
                'externalId' => $review->getCustomerId(),
            ]),
            'externalId' => $review->getReviewId(),
        ];

        $product = $this->getProductByReview($review);

        if ($product !== null) {
            $reviewData['product'] = $this->productAdapter->adaptTo($product);
        }

        /** @var ReviewInterface $adapteeReview */
        $adapteeReview = $this->sentimoReviewFactory->create($reviewData);

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
