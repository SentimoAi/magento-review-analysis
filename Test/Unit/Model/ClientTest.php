<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sentimo\Client\RequestParam\ReviewGetRequestParamBuilder;
use Sentimo\ReviewAnalysis\Model\Client;
use Sentimo\ReviewAnalysis\Model\Config;
use Sentimo\Client\HttpClient\ClientFactory;
use Sentimo\Client\RequestParam\ReviewGetRequestParamBuilderFactory;
use Sentimo\Client\Api\Data\ReviewInterface;
use Sentimo\Client\Exception\LocalizedException;
use GuzzleHttp\Exception\GuzzleException;

class ClientTest extends TestCase
{
    private LoggerInterface $logger;
    private Config $config;
    private ClientFactory $clientFactory;
    private ReviewGetRequestParamBuilderFactory $requestParamBuilderFactory;
    private Client $client;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->config = $this->createMock(Config::class);
        $this->clientFactory = $this->createMock(ClientFactory::class);
        $this->requestParamBuilderFactory = $this->createMock(ReviewGetRequestParamBuilderFactory::class);

        $this->client = new Client(
            $this->logger,
            $this->config,
            $this->clientFactory,
            $this->requestParamBuilderFactory
        );
    }

    public function testPostReviewsSuccessfullyPostsReviews()
    {
        $reviews = [$this->createMock(ReviewInterface::class)];
        $postedReviewIds = [1, 2, 3];

        $apiClient = $this->createMock(\Sentimo\Client\HttpClient\Client::class);

        $this->clientFactory->method('createClient')->willReturn($apiClient);
        $this->config->method('getApiKey')->willReturn('api_key');
        $this->config->method('getChannel')->willReturn('channel');

        $apiClient->method('postReviews')->willReturn($postedReviewIds);
        $apiClient->method('getErrors')->willReturn([]);

        $result = $this->client->postReviews($reviews);

        $this->assertEquals($postedReviewIds, $result);
    }

    public function testPostReviewsLogsErrors()
    {
        $reviews = [$this->createMock(ReviewInterface::class)];
        $errors = ['Error 1', 'Error 2'];

        $apiClient = $this->createMock(\Sentimo\Client\HttpClient\Client::class);
        $apiClient->method('postReviews')->willReturn([]);
        $apiClient->method('getErrors')->willReturn($errors);

        $this->clientFactory->method('createClient')->willReturn($apiClient);
        $this->config->method('getApiKey')->willReturn('api_key');
        $this->config->method('getChannel')->willReturn('channel');

        $this->logger->expects($this->exactly(count($errors)))->method('error');

        $this->client->postReviews($reviews);
    }

    public function testGetReviewsSuccessfullyFetchesReviews()
    {
        $queryParams = ['param1' => 'value1'];
        $reviews = [$this->createMock(ReviewInterface::class)];

        $apiClient = $this->createMock(\Sentimo\Client\HttpClient\Client::class);
        $apiClient->method('getReviews')->willReturn($reviews);

        $this->clientFactory->method('createClient')->willReturn($apiClient);
        $this->config->method('getApiKey')->willReturn('api_key');

        $requestParamBuilder = $this->createMock(ReviewGetRequestParamBuilder::class);
        $this->requestParamBuilderFactory->method('create')->willReturn($requestParamBuilder);

        $result = $this->client->getReviews($queryParams);

        $this->assertEquals($reviews, $result);
    }

    public function testGetReviewsThrowsExceptionOnError()
    {
        $this->expectException(LocalizedException::class);

        $queryParams = ['param1' => 'value1'];

        $apiClient = $this->createMock(\Sentimo\Client\HttpClient\Client::class);
        $apiClient->method('getReviews')->will($this->throwException(new LocalizedException()));

        $this->clientFactory->method('createClient')->willReturn($apiClient);
        $this->config->method('getApiKey')->willReturn('api_key');

        $requestParamBuilder = $this->createMock(ReviewGetRequestParamBuilder::class);
        $this->requestParamBuilderFactory->method('create')->willReturn($requestParamBuilder);

        $this->client->getReviews($queryParams);
    }
}
