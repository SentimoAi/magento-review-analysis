<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_CONFIG_PATH_ACTIVE = 'sentimo_review_analysis/sync/enabled';
    private const XML_CONFIG_PATH_API_KEY = 'sentimo_review_analysis/sync/api_key';
    private const XML_CONFIG_PATH_SYNC_FROM_DATE = 'sentimo_review_analysis/date_range/from_date';
    private const XML_CONFIG_PATH_SYNC_TO_DATE = 'sentimo_review_analysis/date_range/to_date';
    private const XML_CONFIG_PATH_SYNC_CHANNEL = 'sentimo_review_analysis/sync/channel';
    private const XML_CONFIG_PATH_SYNC_API_BASE_URI = 'sentimo_review_analysis/sync/api_base_uri';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor,
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_PATH_ACTIVE, ScopeInterface::SCOPE_WEBSITE);
    }

    public function getApiKey(): string
    {
        return $this->encryptor->decrypt(
            $this->scopeConfig->getValue(self::XML_CONFIG_PATH_API_KEY, ScopeInterface::SCOPE_WEBSITE)
        );
    }

    public function getFromDate(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_PATH_SYNC_FROM_DATE, ScopeInterface::SCOPE_WEBSITE);
    }

    public function getToDate(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_PATH_SYNC_TO_DATE, ScopeInterface::SCOPE_WEBSITE);
    }

    public function getChannel(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_PATH_SYNC_CHANNEL, ScopeInterface::SCOPE_WEBSITE);
    }

    public function getApiBaseUri(): string
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_PATH_SYNC_API_BASE_URI, ScopeInterface::SCOPE_WEBSITE);
    }
}
