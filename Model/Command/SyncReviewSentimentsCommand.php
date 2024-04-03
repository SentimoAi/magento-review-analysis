<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\Command;

use GuzzleHttp\Exception\GuzzleException;
use Sentimo\ReviewAnalysis\Model\Adapter\ReviewAdapter;
use Sentimo\ReviewAnalysis\Model\Client;
use Sentimo\ReviewAnalysis\Model\Config;
use Sentimo\ReviewAnalysis\Model\ReviewProvider;
use Sentimo\ReviewAnalysis\Model\ReviewResource;

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
