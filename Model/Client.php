<?php

declare(strict_types=1);

namespace CustomerFeel\ApiConnector\Model;

use CustomerFeel\ApiConnector\Api\Data\ReviewInterface;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\RequestException;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

class Client
{
    private const BASE_URI = 'http://127.0.0.1:8000/';
    private const JWT_TOKEN_CACHE_KEY  = 'customerfeel_jwt_token';
    private ?string $jwtToken = null;

    private ?\GuzzleHttp\Client $guzzleClient = null;

    /**
     * Todo : custom logger
     * @param ClientFactory $guzzleClientFactory
     * @param CacheInterface $cache
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     * @throws LocalizedException
     */
    public function __construct(
        private readonly \GuzzleHttp\ClientFactory $guzzleClientFactory,
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer,
        private readonly Config $config,
    ) {
        $this->login();
    }

    /**
     * @param ReviewInterface[] $reviews
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function postReviews(array $reviews): array
    {
        $postedReviewIds = [];
        foreach ($reviews as $review) {
            try {
                $response = $this->guzzleClient->post('/api/reviews', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->jwtToken,
                    ],
                    'json' => $review->__serialize(),
                ]);
                if ($response->getStatusCode() !== 201) {
                    throw new LocalizedException(__('Something went wrong when posting review ....'));
                }
                $postedReviewIds[] = $review->getExternalId();
            } catch (RequestException $exception) {
                $this->logger->error(
                    sprintf(
                        'Something went wrong when posting review with id = %s make sure a review with the same external id does not exist in the platform.',
                        $review->getExternalId()
                    )
                );
            }
        }

        return $postedReviewIds;
    }

    public function getReviews(): array
    {
        $response = $this->guzzleClient->get('/api/reviews', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->jwtToken,
                'Accept' => 'application/json',
                'Content-type' => 'application/json'
            ],
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new LocalizedException(__('Something went wrong when get review s....'));
        }

        return $this->serializer->unserialize($response->getBody()->getContents());

    }

    private function isTokenExpired(string $token): bool
    {
        $jwtData = explode('.', $token)[1];
        $jwtData = $this->serializer->unserialize(base64_decode($jwtData));

        $expirationTimestamp = $jwtData['exp'];

        return $expirationTimestamp < time();
    }

    private function getRefreshToken(): string
    {
        return $this->config->getApiKey();
    }

    private function getJwtToken(): ?string
    {
        return $this->cache->load(self::JWT_TOKEN_CACHE_KEY) ?: null;
    }

    private function login(): void
    {
        $this->guzzleClient = $this->guzzleClientFactory->create([
            'config' => [
                'base_uri' => self::BASE_URI
            ]
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
}
