<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\Command;

use Sentimo\ReviewAnalysis\Api\ReviewProviderInterface;
use Sentimo\ReviewAnalysis\Model\Adapter\ReviewAdapter;
use Sentimo\ReviewAnalysis\Model\Client;
use Sentimo\ReviewAnalysis\Model\Config;
use Sentimo\ReviewAnalysis\Model\ResourceModel\ReviewAnalysisSync as ReviewAnalysisSyncResource;
use Sentimo\ReviewAnalysis\Model\ReviewAnalysisSync;

class PostReviewsCommand
{
    public function __construct(
        private readonly Client $client,
        private readonly ReviewProviderInterface $reviewProvider,
        private readonly ReviewAdapter $adapter,
        private readonly Config $config,
        private readonly ReviewAnalysisSyncResource $reviewAnalysisSyncResource,
    ) {
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(): void
    {
        if ($this->config->isEnabled() === false) {
            return;
        }

        $reviews = $this->reviewProvider->getNotSyncedReviews();

        if ($reviews === []) {
            return;
        }

        $reviewsToPost = array_map([$this->adapter, 'adaptTo'], $reviews);

        $reviewIds = $this->client->postReviews($reviewsToPost);

        if ($reviewIds !== []) {
            $this->reviewAnalysisSyncResource->updateReviewsStatus(
                $reviewIds,
                ReviewAnalysisSync::STATUS_IN_PROGRESS
            );
        }
    }
}
