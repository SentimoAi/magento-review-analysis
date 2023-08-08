<?php

declare(strict_types=1);

namespace CustomerFeel\ApiConnector\Model\Command;

use CustomerFeel\ApiConnector\Model\Client;
use CustomerFeel\ApiConnector\Model\Config;
use CustomerFeel\ApiConnector\Model\ReviewAdapter;
use CustomerFeel\ApiConnector\Model\ReviewProvider;
use CustomerFeel\ApiConnector\Model\ReviewResource;
use GuzzleHttp\Exception\GuzzleException;

class PostReviewsCommand
{
    public function __construct(
        private readonly Client         $client,
        private readonly ReviewProvider $reviewProvider,
        private readonly ReviewAdapter  $adapter,
        private readonly Config         $config,
        private readonly ReviewResource $reviewResource,
    ) {
    }

    /**
     * @throws GuzzleException
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
