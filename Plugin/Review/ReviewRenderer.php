<?php

namespace DWD\KiyohReviews\Plugin\Review;

use DWD\KiyohReviews\Model\Get\Reviews;

class ReviewRenderer
{

    protected $_reviewModel;


    public function __construct(
        Reviews $reviewModel
    )
    {
        $this->_reviewModel = $reviewModel;
    }

    public function afterGetReviewsCount(\Magento\Review\Block\Product\ReviewRenderer $subject, $result)
    {
        $productId = $subject->getProduct()->getId();
        return $result + $this->_reviewModel->getProductReviewCount($productId);
    }

    public function afterGetRatingSummary(\Magento\Review\Block\Product\ReviewRenderer $subject, $result)
    {
        $divide = 1;
        $productId = $subject->getProduct()->getId();
        $rating = $this->_reviewModel->getProductReviewRating($productId);

        if ($rating) {
            if ($result) {
                $divide = 2;
            }

            if (!is_null($rating)) {
                $result = ($result + (floatval($rating) * 10)) / $divide;
            }
        }

        return $result;
    }
}
