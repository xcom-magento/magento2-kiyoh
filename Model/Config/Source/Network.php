<?php

namespace DWD\KiyohReviews\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Network implements OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'https://www.klantenvertellen.nl', 'label' => 'www.klantenvertellen.nl'],
            ['value' => 'https://www.kiyoh.com', 'label' => 'www.kiyoh.com'],
        ];
    }
}
