<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model;

use Magento\Framework\Model\AbstractModel;

class ReviewAnalysisSync extends AbstractModel
{
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETE = 'complete';

    protected function _construct()
    {
        $this->_init(\Sentimo\ReviewAnalysis\Model\ResourceModel\ReviewAnalysisSync::class);
    }
}
