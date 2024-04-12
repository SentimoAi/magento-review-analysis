<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Block\Adminhtml\Grid\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Review\Helper\Data;
use Sentimo\ReviewAnalysis\Api\ReviewProviderInterface;
use Sentimo\ReviewAnalysis\Model\Config;

use function in_array;

class Status extends AbstractRenderer
{
    private const SENTIMO_REVIEW_PATH = 'review/redirect/';

    /**
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Sentimo\ReviewAnalysis\Model\Config $config
     * @param \Magento\Review\Helper\Data $reviewHelper
     * @param \Sentimo\ReviewAnalysis\Api\ReviewProviderInterface $reviewProvider
     * @param \Magento\Backend\Block\Context $context
     * @param mixed[] $data
     */
    public function __construct(
        private readonly UrlInterface $urlBuilder,
        private readonly Config $config,
        private readonly Data $reviewHelper,
        private readonly ReviewProviderInterface $reviewProvider,
        Context $context,
        array $data = [],
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row): string
    {
        $reviewAnalysisIds = $this->reviewProvider->getReviewAnalysisSyncCompleteReviewIds();

        $options = $this->reviewHelper->getReviewStatuses();

        if (!in_array($row->getReviewId(), $reviewAnalysisIds, true)) {
            return $options[$row->getStatusId()] ?? '';
        }

        $syncUrl = $this->urlBuilder->getUrl(
            $this->config->getBaseUri() . '/' . self::SENTIMO_REVIEW_PATH . $row->getReviewId()
        );

        if (!isset($options[$row->getStatusId()])) {
            return '';
        }

        return '<a href="' . $syncUrl . '" target="_blank">' . $options[$row->getStatusId()] . '</a>';
    }
}
