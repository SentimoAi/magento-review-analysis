<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\Adapter;

use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\Filter\FilterManager;
use Sentimo\ReviewAnalysis\Api\Data\ProductInterface as SentimoProductInterface;
use Sentimo\ReviewAnalysis\Api\Data\ProductInterfaceFactory as SentimoProductInterfaceFactory;

class ProductAdapter
{
    /**
     * @param \Sentimo\ReviewAnalysis\Api\Data\ProductInterfaceFactory $sentimoProductFactory
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     */
    public function __construct(
        private readonly SentimoProductInterfaceFactory $sentimoProductFactory,
        private readonly AttributeSetRepositoryInterface $attributeSet,
        private readonly FilterManager $filterManager,
    ) {
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return \Sentimo\ReviewAnalysis\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function adaptTo(Product $product): SentimoProductInterface
    {
        $attributeSetRepository = $this->attributeSet->get($product->getAttributeSetId());

        return $this->sentimoProductFactory->create([
            'name' => $product->getName(),
            'price' => (string) $product->getPrice(),
            'description' => $this->filterManager->stripTags($product->getData('description')),
            'product_type' => $attributeSetRepository->getAttributeSetName(),
            'identifier' => $product->getSku(),
        ]);
    }
}
