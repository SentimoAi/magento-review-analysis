<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Plugin;

use Magento\Framework\DataObject;
use Magento\Review\Block\Adminhtml\Grid;
use Sentimo\ReviewAnalysis\Block\Adminhtml\Grid\Renderer\Status;

class ModifyStatusColumnPlugin
{
    /**
     * @param \Magento\Review\Block\Adminhtml\Grid $subject
     * @param string $columnId
     * @param array|\Magento\Framework\DataObject $column
     *
     * @return array
     */
    public function beforeAddColumn(Grid $subject, string $columnId, array|DataObject $column): array
    {
        if ($columnId !== 'status') {
            return [$columnId, $column];
        }

        $column['renderer'] = Status::class;

        return [$columnId, $column];
    }
}
