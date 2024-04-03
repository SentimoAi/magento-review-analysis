<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\Adapter;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\Filter\FilterManager;
use Sentimo\ReviewAnalysis\Api\Data\ProductInterface as SentimoProductInterface;
use Sentimo\ReviewAnalysis\Api\Data\ProductInterfaceFactory as SentimoProductInterfaceFactory;

class ProductAdapter
{
    public function __construct(
        private readonly SentimoProductInterfaceFactory $sentimoProductFactory,
        private readonly AttributeSetRepositoryInterface $attributeSet,
        private readonly FilterManager $filterManager,
    ) {
    }

    public function adaptTo(ProductInterface $product): SentimoProductInterface
    {
        $attributeSetRepository = $this->attributeSet->get($product->getAttributeSetId());

        return $this->sentimoProductFactory->create([
            'name' => $product->getName(),
            'price' => (string) $product->getPrice(),
            'description' => $this->filterManager->stripTags($product->getDescription()),
            'product_type' => $attributeSetRepository->getAttributeSetName(),
            'identifier' => $product->getSku(),
        ]);
    }
}
