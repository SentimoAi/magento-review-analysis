<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\ViewModel;

use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Eav\Api\AttributeSetRepositoryInterface;

class ProductContext implements ArgumentInterface
{
    public function __construct(
        private readonly View $productViewBlock,
        private readonly AttributeSetRepositoryInterface $attributeSetRepository,
        private readonly PriceCurrencyInterface $priceCurrency,
    ) {
    }

    public function getProduct(): ?\Magento\Catalog\Model\Product
    {
        return $this->productViewBlock->getProduct();
    }

    public function getAttributeSetName(?Product $product): ?string
    {
        $attributeSetId = $product?->getAttributeSetId();
        if ($attributeSetId) {
            try {
                $attributeSet = $this->attributeSetRepository->get($attributeSetId);
                return $attributeSet->getAttributeSetName();
            } catch (\Exception $e) {
                // Handle exception if needed
            }
        }
        return null;
    }

    public function getCurrencyCode(): string
    {
        return $this->priceCurrency->getCurrency()->getCurrencyCode();
    }

    public function getProductDescription(?Product $product): ?string
    {
        if ($product && $product->getDescription()) {
            return (string)$product->getDescription();
        }

        return null;
    }
}
