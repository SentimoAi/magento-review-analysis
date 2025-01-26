<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\Command;

use Magento\Framework\App\ResourceConnection;
use Sentimo\ReviewAnalysis\Api\ReviewProviderInterface;
use Sentimo\ReviewAnalysis\Model\Client;
use Sentimo\ReviewAnalysis\Model\Config;
use Sentimo\ReviewAnalysis\Model\RequestParam\ReviewGetRequestParamBuilderInterface;
use Sentimo\ReviewAnalysis\Model\ResourceModel\ReviewAnalysisSync;
use Sentimo\ReviewAnalysis\Model\ReviewAnalysisSync as ReviewAnalysisSyncModel;
use Sentimo\ReviewAnalysis\Model\ReviewStatusHandler;

class SyncReviewsCommand
{
    private const BATCH_SIZE = 100;

    public function __construct(
        private readonly Client $client,
        private readonly ReviewGetRequestParamBuilderInterface $reviewGetRequestParamBuilder,
        private readonly Config $config,
        private readonly ReviewStatusHandler $reviewStatusHandler,
        private readonly ResourceConnection $resourceConnection,
        private readonly ReviewAnalysisSync $reviewAnalysisSyncResource,
        private readonly ReviewProviderInterface $reviewProvider
    ) {
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Throwable
     */
    public function execute(): void
    {
        if ($this->config->isEnabled() === false) {
            return;
        }

        $reviewIds = $this->reviewProvider->getSyncInProgressReviewIds();
        $batches = $this->splitIntoBatches($reviewIds, self::BATCH_SIZE);

        $sentimoReviews = [];

        foreach ($batches as $batch) {
            $batchReviews = $this->client->getReviews(
                $this->reviewGetRequestParamBuilder->buildRequestParam($batch),
                true
            );

            foreach ($batchReviews as $review) {
                $sentimoReviews[] = $review; // Append reviews directly
            }
        }

        $connection = $this->resourceConnection->getConnection();
        $connection->beginTransaction();

        try {
            $this->reviewStatusHandler->updateStatuses($sentimoReviews);
            $this->reviewAnalysisSyncResource->updateReviewsStatus(
                $this->getReviewIds($sentimoReviews),
                ReviewAnalysisSyncModel::STATUS_COMPLETE
            );
        } catch (\Throwable $exception) {
            $connection->rollBack();

            throw $exception;
        }

        $connection->commit();
    }

    /**
     * Divides an array into batches.
     *
     * @param array $items
     * @param int $batchSize
     *
     * @return array
     */
    private function splitIntoBatches(array $items, int $batchSize): array
    {
        return array_chunk($items, $batchSize);
    }

    /**
     * @param \Sentimo\Client\Api\Data\Review[] $sentimoReviews
     *
     * @return int[]
     */
    private function getReviewIds(array $sentimoReviews): array
    {
        $reviewIds = [];

        foreach ($sentimoReviews as $sentimoReview) {
            if ($sentimoReview->getExternalId() !== null) {
                $reviewIds[] = (int) $sentimoReview->getExternalId();
            }
        }

        return $reviewIds;
    }
}
