<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model;

use Psr\Log\LoggerInterface;
use Sentimo\Client\RequestParam\ReviewGetRequestParamBuilderFactory;
use Sentimo\Client\HttpClient\ClientFactory as SentimoClientFactory;

class Client
{
    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Sentimo\ReviewAnalysis\Model\Config $config
     * @param \Sentimo\Client\HttpClient\ClientFactory $clientFactory
     * @param \Sentimo\Client\RequestParam\ReviewGetRequestParamBuilderFactory $requestParamBuilderFactory
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Config $config,
        private readonly SentimoClientFactory $clientFactory,
        private readonly ReviewGetRequestParamBuilderFactory $requestParamBuilderFactory,
    ) {
    }

    /**
     * @param \Sentimo\Client\Api\Data\ReviewInterface[] $reviews
     *
     * @return int[]
     * @throws \Sentimo\Client\Exception\LocalizedException
     */
    public function postReviews(array $reviews): array
    {
        $client = $this->clientFactory->createClient($this->config->getApiKey());
        $postedReviewIds = $client->postReviews($reviews, $this->config->getChannel());

        foreach ($client->getErrors() as $error) {
            $this->logger->error($error);
        }

        return $postedReviewIds;
    }

    /**
     * @param array<string,string|int|string[]> $queryParams
     * @param bool $fetchAll
     *
     * @return \Sentimo\Client\Api\Data\ReviewInterface[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Sentimo\Client\Exception\LocalizedException
     */
    public function getReviews(array $queryParams, bool $fetchAll = false): array
    {
        $client = $this->clientFactory->createClient($this->config->getApiKey());

        $requestParamBuilder = $this->requestParamBuilderFactory->create(['params' => $queryParams]);

        return $client->getReviews($requestParamBuilder, $fetchAll);
    }
}
