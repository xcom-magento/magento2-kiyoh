<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace DWD\KiyohReviews\Block\Product;

use DWD\KiyohReviews\Model\Get\Reviews;
use Magento\Framework\View\Element\Template\Context;

class View extends \Magento\Framework\View\Element\Template
{
    protected $_reviewModel;

    public function __construct(
        Context $context,
        Reviews $reviewModel,

        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_reviewModel = $reviewModel;
    }

    /**
     * @return array
     */
    public function getReviewByProduct()
    {
        return $this->_reviewModel->getProductReviews();
    }

    /**
     * @param string| int $rating
     * @return int
     */
    public function getPercentage($rating)
    {
        return intval($rating) * 10;
    }

}
