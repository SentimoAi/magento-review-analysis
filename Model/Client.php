<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model;

use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\RequestException;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Url\DecoderInterface;
use Psr\Log\LoggerInterface;
use Sentimo\ReviewAnalysis\Model\RequestParam\CompositeReviewPostRequestParamBuilder;

use function __;
use function explode;
use function is_array;
use function parse_str;
use function parse_url;
use function sprintf;
use function time;

class Client
{
    private const JWT_TOKEN_CACHE_KEY = 'sentimo_review_analysis_jwt_token';

    private ?string $jwtToken = null;

    private ?\GuzzleHttp\Client $guzzleClient = null;

    /**
     * @param \GuzzleHttp\ClientFactory $guzzleClientFactory
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Framework\Url\DecoderInterface $urlDecoder
     * @param \Sentimo\ReviewAnalysis\Model\Config $config
     * @param \Sentimo\ReviewAnalysis\Model\RequestParam\CompositeReviewPostRequestParamBuilder $compositeReviewRequestParamBuilder
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        private readonly ClientFactory $guzzleClientFactory,
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer,
        private readonly DecoderInterface $urlDecoder,
        private readonly Config $config,
        private readonly CompositeReviewPostRequestParamBuilder $compositeReviewRequestParamBuilder,
    ) {
    }

    /**
     * @param \Sentimo\ReviewAnalysis\Api\Data\SentimoReviewInterface[] $reviews
     *
     * @return int[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postReviews(array $reviews): array
    {
        $this->login();
        $postedReviewIds = [];

        foreach ($reviews as $review) {
            $responseBody = '';
            $errorOccurred = false;
            $errorMessage = '';

            try {
                $response = $this->guzzleClient->post('/api/reviews', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->jwtToken,
                        'Accept' => 'application/ld+json',
                        'Content-type' => 'application/ld+json',
                    ],
                    'json' => $this->compositeReviewRequestParamBuilder->buildRequestParam($review),
                ]);

                $responseBody = $response->getBody()->getContents();

                if ($response->getStatusCode() !== 201) {
                    throw new LocalizedException(
                        __(
                            'Failed to post review, HTTP Status Code: %1',
                            $response->getStatusCode()
                        )
                    );
                }

                $responseData = $this->serializer->unserialize($responseBody);
                $externalId = $responseData['externalId'] ?? null;

                if ($externalId === null) {
                    throw new LocalizedException(__('External ID not found in response: %1', $responseBody));
                }

                $postedReviewIds[] = $externalId;
            } catch (RequestException $exception) {
                $responseBody = $exception->getResponse()?->getBody()->getContents() ?? 'No response body';
                $errorOccurred = true;
                $errorMessage = $exception->getMessage();
            } catch (\Throwable $exception) {
                $errorOccurred = true;
                $errorMessage = $exception->getMessage();
            } finally {
                if ($errorOccurred) {
                    $this->logger->error(
                        sprintf(
                            'Error posting review with external ID = %s: %s Response: %s',
                            $review->getExternalId() ?? 'N/A',
                            $errorMessage,
                            $responseBody
                        )
                    );
                }
            }
        }

        return $postedReviewIds;
    }

    /**
     * @param array<string,string|int|string[]> $queryParams
     * @param bool $fetchAll
     *
     * @return array<string,string|int|string[]>
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getReviews(array $queryParams, bool $fetchAll = false): array
    {
        $this->login();
        $allReviews = [];

        do {
            $response = $this->guzzleClient->get('/api/reviews', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->jwtToken,
                    'Accept' => 'application/ld+json',
                    'Content-type' => 'application/ld+json',
                ],
                'query' => $queryParams,
            ]);

            // Check for a successful response
            if ($response->getStatusCode() !== 200) {
                throw new LocalizedException(
                    __(
                        'Failed to fetch reviews, HTTP Status Code: %1',
                        $response->getStatusCode()
                    )
                );
            }

            $content = $response->getBody()->getContents();
            $decodedContent = $this->serializer->unserialize($content);

            // Ensure the decoded content has the expected structure
            if (!isset($decodedContent['hydra:member']) || !is_array($decodedContent['hydra:member'])) {
                throw new LocalizedException(__('Unexpected response structure.'));
            }

            $reviews = $decodedContent['hydra:member'];

            foreach ($reviews as $review) {
                $allReviews[] = $review;
            }

            // Determine if there's a next page
            $nextPage = $decodedContent['hydra:view']['hydra:next'] ?? null;

            if ($fetchAll && $nextPage) {
                parse_str(parse_url($nextPage, PHP_URL_QUERY), $nextPageParams); //phpcs:ignore

                foreach ($nextPageParams as $key => $value) {
                    $queryParams[$key] = $value;
                }
            } else {
                $nextPage = null;
            }
        } while ($fetchAll && $nextPage);

        return $allReviews;
    }

    private function isTokenExpired(string $token): bool
    {
        $jwtData = explode('.', $token)[1];
        $jwtData = $this->serializer->unserialize($this->urlDecoder->decode($jwtData));

        $expirationTimestamp = $jwtData['exp'];

        return $expirationTimestamp < time();
    }

    private function getRefreshToken(): string
    {
        return $this->config->getApiKey();
    }

    private function login(): void
    {
        $this->guzzleClient = $this->guzzleClientFactory->create([
            'config' => [
                'base_uri' => $this->config->getApiBaseUri(),
            ],
        ]);
        $this->jwtToken = $this->getJwtToken();

        if ($this->jwtToken === null || $this->isTokenExpired($this->jwtToken)) {
            $response = $this->guzzleClient->post('/api/token/refresh', [
                'form_params' => [
                    'refresh_token' => $this->getRefreshToken(),
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new LocalizedException(__('Something went wrong with your request.'));
            }

            $responseData = $this->serializer->unserialize($response->getBody()->getContents());
            $this->jwtToken = $responseData['token'];

            $this->cache->save($this->jwtToken, self::JWT_TOKEN_CACHE_KEY, ['config'], 3600);
        }
    }

    private function getJwtToken(): ?string
    {
        return $this->cache->load(self::JWT_TOKEN_CACHE_KEY) ?: null;
    }
}
