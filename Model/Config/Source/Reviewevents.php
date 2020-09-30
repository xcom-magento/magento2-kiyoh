<?php

namespace DWD\KiyohReviews\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Reviewevents implements OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'complete', 'label' => 'complete'],
            ['value' => 'processing', 'label' => 'processing'],
        ];
    }
}
