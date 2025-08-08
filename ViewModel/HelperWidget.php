<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class HelperWidget implements ArgumentInterface
{
    private const XML_PATH_API_KEY = 'sentimo_review_analysis/review_helper/api_key';
    private const XML_PATH_REVIEW_FIELD_SELECTOR = 'sentimo_review_analysis/review_helper/review_field_selector';
    private const XML_PATH_TOOLTIP_TEXT = 'sentimo_review_analysis/review_helper/tooltip_text';
    private const XML_PATH_USE_SUGGESTION_BUTTON_LABEL = 'sentimo_review_analysis/review_helper/use_suggestion_button_label';
    private const XML_PATH_CONTINUE_BUTTON_LABEL = 'sentimo_review_analysis/review_helper/continue_button_label';
    private const XML_PATH_CANCEL_BUTTON_LABEL = 'sentimo_review_analysis/review_helper/cancel_button_label';
    private const XML_PATH_SEE_SUGGESTION_LINK_LABEL = 'sentimo_review_analysis/review_helper/see_suggestion_link_label';
    private const XML_PATH_MESSAGE_TOO_SHORT = 'sentimo_review_analysis/review_helper/message_too_short';
    private const XML_PATH_MESSAGE_LOADING = 'sentimo_review_analysis/review_helper/message_loading';
    private const XML_PATH_MESSAGE_SUCCESS = 'sentimo_review_analysis/review_helper/message_success';
    private const XML_PATH_MESSAGE_ERROR = 'sentimo_review_analysis/review_helper/message_error';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function getApiKey(): string
    {
        $apiKey = (string)$this->scopeConfig->getValue(self::XML_PATH_API_KEY, ScopeInterface::SCOPE_WEBSITE);

        return $this->encryptor->decrypt($apiKey);
    }

    public function getWidgetConfig(): array
    {
        $config = [
            'reviewFieldSelector' => $this->scopeConfig->getValue(
                self::XML_PATH_REVIEW_FIELD_SELECTOR,
                ScopeInterface::SCOPE_WEBSITE
            )
        ];

        if ($tooltip = $this->scopeConfig->getValue(self::XML_PATH_TOOLTIP_TEXT, ScopeInterface::SCOPE_WEBSITE)) {
            $config['tooltipText'] = $tooltip;
        }

        if ($label = $this->scopeConfig->getValue(self::XML_PATH_USE_SUGGESTION_BUTTON_LABEL, ScopeInterface::SCOPE_WEBSITE)) {
            $config['useSuggestionButtonLabel'] = $label;
        }

        if ($label = $this->scopeConfig->getValue(self::XML_PATH_CONTINUE_BUTTON_LABEL, ScopeInterface::SCOPE_WEBSITE)) {
            $config['continueButtonLabel'] = $label;
        }

        if ($label = $this->scopeConfig->getValue(self::XML_PATH_CANCEL_BUTTON_LABEL, ScopeInterface::SCOPE_WEBSITE)) {
            $config['cancelButtonLabel'] = $label;
        }

        if ($label = $this->scopeConfig->getValue(self::XML_PATH_SEE_SUGGESTION_LINK_LABEL, ScopeInterface::SCOPE_WEBSITE)) {
            $config['seeSuggestionLinkLabel'] = $label;
        }

        $messages = array_filter([
            'tooShort' => $this->scopeConfig->getValue(self::XML_PATH_MESSAGE_TOO_SHORT, ScopeInterface::SCOPE_WEBSITE),
            'loading' => $this->scopeConfig->getValue(self::XML_PATH_MESSAGE_LOADING, ScopeInterface::SCOPE_WEBSITE),
            'success' => $this->scopeConfig->getValue(self::XML_PATH_MESSAGE_SUCCESS, ScopeInterface::SCOPE_WEBSITE),
            'error' => $this->scopeConfig->getValue(self::XML_PATH_MESSAGE_ERROR, ScopeInterface::SCOPE_WEBSITE),
        ]);

        if ($messages) {
            $config['messages'] = $messages;
        }

        return $config;
    }

    public function getConfigJson(array $additionalConfig = []): string
    {
        $config = array_merge(
            $this->getWidgetConfig(),
            ['apiKey' => $this->getApiKey()],
            $additionalConfig
        );

        return $this->serializer->serialize($config);
    }
}
