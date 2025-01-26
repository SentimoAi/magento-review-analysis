<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\RequestParam;

use Sentimo\ReviewAnalysis\Api\ReviewProviderInterface;

class ReviewGetRequestParamBuilder implements ReviewGetRequestParamBuilderInterface
{
    /**
     * @inheritDoc
     */
    public function buildRequestParam(array $reviewIds): array
    {
        return [
            'externalId' => $reviewIds,
            'exists' => ['moderationStatus' => true],
        ];
    }
}
