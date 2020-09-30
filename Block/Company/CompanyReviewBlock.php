<?php

namespace DWD\KiyohReviews\Block\Company;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use \DWD\KiyohReviews\Model\Get\CompanyReview;

class CompanyReviewBlock extends Template
{
    public $ratingString = null;

    protected $companyReview;

    public function __construct(
        Context $context,
        CompanyReview $companyReview
    )
    {
        $this->companyReview = $companyReview;
        parent::__construct($context);
    }

    public function setCompanyReview()
    {
        $this->ratingString = $this->companyReview->gatherReviews();
    }

    public function getCorrectData()
    {
        if (is_array($this->ratingString) && count($this->ratingString)) {
            return true;
        }
        return false;
    }

    public function getRatingPercentage()
    {
        if (isset($this->ratingString['averageRating'])) {
            $val = floatval($this->ratingString['averageRating']);
            return ($val * 10);
        }
        return false;
    }

    public function getMaxrating()
    {
        return 10;
    }

    public function getReviews()
    {
        if (isset($this->ratingString['numberReviews'])) {
            return $this->ratingString['numberReviews'];
        }
        return false;
    }

    public function getRating()
    {
        if (isset($this->ratingString['averageRating'])) {
            return $this->ratingString['averageRating'];
        }
        return false;
    }

    public function getMicrodataUrl()
    {
        if (isset($this->ratingString['viewReviewUrl'])) {
            return $this->ratingString['viewReviewUrl'];
        }
        return false;
    }

    public function getLocationName(){
        if (isset($this->ratingString['locationName'])) {
            return $this->ratingString['locationName'];
        }
        return false;
    }
}
