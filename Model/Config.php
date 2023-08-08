<?php

declare(strict_types=1);

namespace CustomerFeel\ApiConnector\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Config
{
    private const XML_CONFIG_PATH_ACTIVE = 'customer_feel_api/sync/active';
    private const XML_CONFIG_PATH_API_KEY = 'customer_feel_api/sync/api_key';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor,
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_PATH_ACTIVE);
    }

    public function getApiKey(): string
    {
        return  $this->encryptor->decrypt($this->scopeConfig->getValue(self::XML_CONFIG_PATH_API_KEY));
    }
}
