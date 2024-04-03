<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\RequestParam;

use Magento\Framework\Exception\LocalizedException;
use Sentimo\ReviewAnalysis\Api\Data\ReviewInterface;

use function __;
use function array_merge_recursive;

class CompositeReviewRequestParamBuilder implements ReviewRequestParamBuilderInterface
{
    /**
     * @param \Sentimo\ReviewAnalysis\Model\RequestParam\ReviewRequestParamBuilderInterface[] $requestParamBuilders
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
    public function buildRequestParam(ReviewInterface $review): array
    {
        $requestParam = [];

        foreach ($this->requestParamBuilders as $requestParamBuilder) {
            if (!$requestParamBuilder instanceof ReviewRequestParamBuilderInterface) {
                throw new LocalizedException(
                    __(
                        '%1 must be an instance of %2',
                        $requestParamBuilder::class,
                        ReviewRequestParamBuilderInterface::class,
                    )
                );
            }

            $requestParam [] = $requestParamBuilder->buildRequestParam($review);
        }

        return array_merge_recursive(...$requestParam);
    }
}
