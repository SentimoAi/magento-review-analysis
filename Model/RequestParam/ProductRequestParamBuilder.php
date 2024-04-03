<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\RequestParam;

use Sentimo\ReviewAnalysis\Api\Data\ReviewInterface;

class ProductRequestParamBuilder implements ReviewRequestParamBuilderInterface
{
    /**
     * @inheritDoc
     */
    public function buildRequestParam(ReviewInterface $review): array
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
