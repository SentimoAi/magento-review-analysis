<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\Command;

use Sentimo\ReviewAnalysis\Model\Adapter\ReviewAdapter;
use Sentimo\ReviewAnalysis\Model\Client;
use Sentimo\ReviewAnalysis\Model\Config;
use Sentimo\ReviewAnalysis\Model\ReviewProvider;
use Sentimo\ReviewAnalysis\Model\ReviewResource;

class PostReviewsCommand
{
    public function __construct(
        private readonly Client $client,
        private readonly ReviewProvider $reviewProvider,
        private readonly ReviewAdapter $adapter,
        private readonly Config $config,
        private readonly ReviewResource $reviewResource,
    ) {
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute(): void
    {
        if ($this->config->isEnabled() === false) {
            return;
        }

        $reviewsToPost = [];

        foreach ($this->reviewProvider->getReviewsToSync() as $review) {
            $reviewsToPost[] = $this->adapter->adaptTo($review);
        }

        $reviewIds = $this->client->postReviews($reviewsToPost);
        $this->reviewResource->updateReviewsStatus($reviewIds, ReviewResource::STATUS_IN_PROGRESS);
    }
}
