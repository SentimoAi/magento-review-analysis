<?php

declare(strict_types=1);

namespace Sentimo\ReviewAnalysis\Block\Systrem\Config\Form;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Stdlib\DateTime;

class Date extends Field
{
    public function render(AbstractElement $element): string
    {
        $element->setDateFormat(DateTime::DATE_INTERNAL_FORMAT);

        return parent::render($element);
    }
}
