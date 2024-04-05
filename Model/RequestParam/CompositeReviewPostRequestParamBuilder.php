<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\RequestParam;

use Magento\Framework\Exception\LocalizedException;
use Sentimo\ReviewAnalysis\Api\Data\SentimoReviewInterface;

use function __;
use function array_merge_recursive;

class CompositeReviewPostRequestParamBuilder implements ReviewPostRequestParamBuilderInterface
{
    /**
     * @param \Sentimo\ReviewAnalysis\Model\RequestParam\ReviewPostRequestParamBuilderInterface[] $requestParamBuilders
     */
    public function __construct(
        private readonly array $requestParamBuilders = []
    ) {
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @inheritDoc
     */
    public function buildRequestParam(SentimoReviewInterface $review): array
    {
        $requestParam = [];

        foreach ($this->requestParamBuilders as $requestParamBuilder) {
            if (!$requestParamBuilder instanceof ReviewPostRequestParamBuilderInterface) {
                throw new LocalizedException(
                    __(
                        '%1 must be an instance of %2',
                        $requestParamBuilder::class,
                        ReviewPostRequestParamBuilderInterface::class,
                    )
                );
            }

            $requestParam [] = $requestParamBuilder->buildRequestParam($review);
        }

        return array_merge_recursive(...$requestParam);
    }
}
