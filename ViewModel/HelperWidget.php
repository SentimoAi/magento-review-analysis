<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class HelperWidget implements ArgumentInterface
{
    private const XML_PATH_API_KEY = 'sentimo_review_analysis/review_helper/api_key';
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor,
    ) {
    }

    public function getApiKey(): string
    {
        $apiKey = $this->scopeConfig->getValue(self::XML_PATH_API_KEY, ScopeInterface::SCOPE_WEBSITE);

        return $this->encryptor->decrypt($apiKey);
    }
}
