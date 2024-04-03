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
use Sentimo\ReviewAnalysis\Model\RequestParam\CompositeReviewRequestParamBuilder;

use function __;
use function explode;
use function sprintf;
use function time;

class Client
{
    private const JWT_TOKEN_CACHE_KEY = 'sentimo_review_analysis_jwt_token';

    private ?string $jwtToken = null;

    private ?\GuzzleHttp\Client $guzzleClient = null;

    /**
     * Todo : custom logger
     *
     * @param \GuzzleHttp\ClientFactory $guzzleClientFactory
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
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
        private readonly CompositeReviewRequestParamBuilder $compositeReviewRequestParamBuilder,
    ) {
        $this->login();
    }

    /**
     * @param \Sentimo\ReviewAnalysis\Api\Data\ReviewInterface[] $reviews
     *
     * @return int[]
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
                        'Accept' => 'application/ld+json',
                        'Content-type' => 'application/ld+json',
                    ],
                    'json' => $this->compositeReviewRequestParamBuilder->buildRequestParam($review),
                ]);

                if ($response->getStatusCode() !== 201) {
                    throw new LocalizedException(__($response->getBody()->getContents()));
                }

                $postedReviewIds[] = $review->getExternalId();
            } catch (RequestException $exception) {
                $this->logger->error(
                    sprintf(
                        'Something went wrong when posting review with id = %s
                        make sure a review with the same external id does not exist in the platform.',
                        $review->getExternalId()
                    ),
                    $exception->getTrace()
                );
            } catch (\Throwable $exception) {
                $this->logger->critical($exception->getMessage(), $exception->getTrace());
            }
        }

        //Todo : handle errors
        return $postedReviewIds;
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getReviews(): array
    {
        $response = $this->guzzleClient->get('/api/reviews', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->jwtToken,
                'Accept' => 'application/json',
                'Content-type' => 'application/json',
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
