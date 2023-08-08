<?php

declare(strict_types=1);

namespace CustomerFeel\ApiConnector\Model\Command;

use CustomerFeel\ApiConnector\Model\Client;
use CustomerFeel\ApiConnector\Model\Config;
use CustomerFeel\ApiConnector\Model\ReviewAdapter;
use CustomerFeel\ApiConnector\Model\ReviewProvider;
use CustomerFeel\ApiConnector\Model\ReviewResource;
use GuzzleHttp\Exception\GuzzleException;

class SyncReviewSentimentsCommand
{
    public function __construct(
        private readonly Client         $client,
        private readonly ReviewProvider $reviewProvider,
        private readonly ReviewAdapter  $adapter,
        private readonly Config         $config,
        private readonly ReviewResource $reviewStatus,
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

        $reviews = [];
        foreach ($this->client->getReviews() as $review) {
            $reviews[] = $this->adapter->adaptFrom($review);
        }
    }
}
