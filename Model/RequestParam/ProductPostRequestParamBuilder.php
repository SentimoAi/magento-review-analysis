<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\RequestParam;

use Sentimo\ReviewAnalysis\Api\Data\SentimoReviewInterface;

class ProductPostRequestParamBuilder implements ReviewPostRequestParamBuilderInterface
{
    /**
     * @inheritDoc
     */
    public function buildRequestParam(SentimoReviewInterface $review): array
    {
        $product = $review->getProduct();

        if ($product === null) {
            return [];
        }

        return [
            'product' => [
                'name' => $product->getName(),
                'description' => $product->getDescription() ?? '',
                'price' => $product->getPrice() ?? '',
                'identifier' => $product->getIdentifier() ?? '',
                'productType' => $product->getProductType() ?? '',
            ],
        ];
    }
}
