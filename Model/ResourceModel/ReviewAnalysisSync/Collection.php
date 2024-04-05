<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Model\ResourceModel\ReviewAnalysisSync;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Sentimo\ReviewAnalysis\Model\ReviewAnalysisSync;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            ReviewAnalysisSync::class,
            \Sentimo\ReviewAnalysis\Model\ResourceModel\ReviewAnalysisSync::class
        );
    }
}
