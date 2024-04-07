<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Test\Unit\Model;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Psr7\Response;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sentimo\ReviewAnalysis\Model\Client;
use Sentimo\ReviewAnalysis\Model\Config;
use Sentimo\ReviewAnalysis\Model\RequestParam\CompositeReviewPostRequestParamBuilder;
use Sentimo\ReviewAnalysis\Model\SentimoReview;

use function json_encode;

class ClientTest extends TestCase
{
    private $client;
    private $guzzleClientMock;
    private $serializerMock;
    private $compositeReviewRequestParamBuilderMock;

    public function testPostReviewsSuccessfullyPostsReviews(): void
    {
        $reviewMock = $this->createMock(SentimoReview::class);
        $reviewMock->setExternalId('123');
        $reviews = [$reviewMock];

        $responseBody = json_encode(['externalId' => '123']); //phpcs:ignore
        $responseToken = json_encode(['token' => '123']); //phpcs:ignore

        $this->guzzleClientMock->expects($this->exactly(2))
            ->method('post')
            ->willReturnOnConsecutiveCalls(
                new Response(200, [], $responseToken),
                new Response(201, [], $responseBody)
            );

        $this->serializerMock->expects($this->exactly(2))
            ->method('unserialize')
            ->willReturnOnConsecutiveCalls(['token' => '123'], ['externalId' => '123']);

        $this->compositeReviewRequestParamBuilderMock
            ->method('buildRequestParam')
            ->willReturn([]);

        $result = $this->client->postReviews($reviews);

        $this->assertEquals(['123'], $result);
    }

    public function testPostReviewsDoesNotPostReviewWhenStatusCodeIsNot201(): void
    {
        $reviewMock = $this->createMock(SentimoReview::class);
        $reviewMock->setExternalId('123');

        $reviews = [$reviewMock];

        $responseBody = json_encode(['externalId' => '123']); //phpcs:ignore
        $responseToken = json_encode(['token' => '123']); //phpcs:ignore

        $this->guzzleClientMock
            ->method('post')
            ->willReturnOnConsecutiveCalls(new Response(200, [], $responseToken), new Response(400, [], $responseBody));

        $this->serializerMock
            ->method('unserialize')
            ->willReturn(['token' => '123']);

        $result = $this->client->postReviews($reviews);

        $this->assertEquals([], $result);
    }

    public function testPostReviewsDoesNotPostReviewWhenExternalIdIsMissing(): void
    {
        $reviewMock = $this->createMock(SentimoReview::class);
        $reviewMock->setExternalId('123');

        $reviews = [$reviewMock];

        $responseBody = json_encode([]); //phpcs:ignore
        $responseToken = json_encode(['token' => '123']); //phpcs:ignore

        $this->guzzleClientMock
            ->method('post')
            ->willReturnOnConsecutiveCalls(new Response(200, [], $responseToken), new Response(201, [], $responseBody));

        $this->serializerMock
            ->method('unserialize')
            ->willReturn(['token' => '123']);

        $result = $this->client->postReviews($reviews);

        $this->assertEquals([], $result);
    }

    public function testGetReviewsSuccessfullyGetsReviews(): void
    {
        $responseBody = json_encode(['hydra:member' => [['externalId' => '123']]]); //phpcs:ignore
        $responseToken = json_encode(['token' => '123']); //phpcs:ignore

        $this->guzzleClientMock
            ->method('post')
            ->willReturn(new Response(200, [], $responseToken));

        $this->guzzleClientMock
            ->method('get')
            ->willReturn(new Response(200, [], $responseBody));

        $this->serializerMock->expects($this->exactly(2))
            ->method('unserialize')
            ->willReturnOnConsecutiveCalls(['token' => '123'], ['hydra:member' => [['externalId' => '123']]]);

        $result = $this->client->getReviews([]);

        $this->assertEquals([['externalId' => '123']], $result);
    }

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->guzzleClientMock = $this->createMock(GuzzleClient::class);
        $guzzleClientFactoryMock = $this->createMock(ClientFactory::class);
        $guzzleClientFactoryMock->method('create')->willReturn($this->guzzleClientMock);

        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->compositeReviewRequestParamBuilderMock = $this->createMock(
            CompositeReviewPostRequestParamBuilder::class
        );

        $this->client = $objectManager->getObject(Client::class, [
            'guzzleClientFactory' => $guzzleClientFactoryMock,
            'cache' => $this->createMock(CacheInterface::class),
            'logger' => $this->createMock(LoggerInterface::class),
            'serializer' => $this->serializerMock,
            'config' => $this->createMock(Config::class),
            'compositeReviewRequestParamBuilder' => $this->compositeReviewRequestParamBuilderMock,
        ]);
    }
}
