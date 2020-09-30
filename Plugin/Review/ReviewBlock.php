<?php

namespace DWD\KiyohReviews\Plugin\Review;

use DWD\KiyohReviews\Model\Get\Reviews;

class ReviewBlock
{

    protected $_reviewModel;

    public function __construct(
        Reviews $reviewModel
    )
    {
        $this->_reviewModel = $reviewModel;
    }

    public function afterGetCollectionSize(\Magento\Review\Block\Product\Review $subject, $result)
    {
        return $result + $this->_reviewModel->getProductReviewCount();
    }
}
