<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\Command;

use Magento\Framework\App\ResourceConnection;
use Sentimo\ReviewAnalysis\Model\Client;
use Sentimo\ReviewAnalysis\Model\Config;
use Sentimo\ReviewAnalysis\Model\RequestParam\ReviewGetRequestParamBuilderInterface;
use Sentimo\ReviewAnalysis\Model\ResourceModel\ReviewAnalysisSync;
use Sentimo\ReviewAnalysis\Model\ReviewAnalysisSync as ReviewAnalysisSyncModel;
use Sentimo\ReviewAnalysis\Model\ReviewStatusHandler;

class SyncReviewSentimentsCommand
{
    public function __construct(
        private readonly Client $client,
        private readonly ReviewGetRequestParamBuilderInterface $reviewGetRequestParamBuilder,
        private readonly Config $config,
        private readonly ReviewStatusHandler $reviewStatusHandler,
        private readonly ResourceConnection $resourceConnection,
        private readonly ReviewAnalysisSync $reviewAnalysisSyncResource,
    ) {
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute(): void
    {
        if ($this->config->isEnabled() === false) {
            return;
        }

        $sentimoReviews = $this->client->getReviews($this->reviewGetRequestParamBuilder->buildRequestParam(), true);

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
     * @param array<string,string|int|string[]> $sentimoReviews
     *
     * @return int[]
     */
    private function getReviewIds(array $sentimoReviews): array
    {
        $reviewIds = [];

        foreach ($sentimoReviews as $sentimoReview) {
            if (isset($sentimoReview['externalId'])) {
                $reviewIds[] = $sentimoReview['externalId'];
            }
        }

        return $reviewIds;
    }
}
