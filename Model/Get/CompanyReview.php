<?php

namespace DWD\KiyohReviews\Model\Get;

use Magento\Catalog\Helper\Data as CatalogHelper;
use \Magento\Framework\App\CacheInterface;
use \DWD\KiyohReviews\Helper\Logger;
use DWD\KiyohReviews\Helper\Data as HelperData;

class CompanyReview
{
    protected $_catalogHelper;

    protected $helperData;

    protected $_logger;

    const REVIEW_CACHE_KEY = 'dwd_kiyoh_company_reviews';

    protected $_cache;

    public function __construct(
        CatalogHelper $helper,
        HelperData $helperData,
        CacheInterface $cache,
        Logger $logger
    )
    {
        $this->_cache = $cache;
        $this->_logger = $logger;
        $this->helperData = $helperData;
        $this->_catalogHelper = $helper;
    }

    public function gatherReviews()
    {
        $reviews = $this->_cache->load(self::REVIEW_CACHE_KEY);
        if (!$reviews) {
            $locationId = $this->helperData->getKiyohLocationId();
            $url = $this->helperData->getKiyohReviewCompanyFeed() . '?locationId=' . $locationId;

            $apiKey = $this->helperData->getKiyohApiKey();

            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "X-Publication-Api-Token: " . $apiKey,
                ),
            ));
            $reviews = curl_exec($ch);
            if (curl_errno($ch)) {
                $this->_logger->customLog(
                    'DWD Curl error: ' . basename(__FILE__) . ' ' . curl_error($ch)
                );
            } else {
                $this->_cache->save(serialize($reviews), self::REVIEW_CACHE_KEY, array(), 3600);
            }
        } else {
            $reviews = unserialize($reviews);
        }
        return json_decode($reviews, true);
    }
}
