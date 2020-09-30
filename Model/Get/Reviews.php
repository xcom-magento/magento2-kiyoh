<?php

namespace DWD\KiyohReviews\Model\Get;

use Magento\Catalog\Helper\Data as CatalogHelper;
use \Magento\Framework\App\CacheInterface;
use \DWD\KiyohReviews\Helper\Logger;
use DWD\KiyohReviews\Helper\Data as HelperData;

class Reviews
{
    protected $_catalogHelper;

    protected $helperData;

    protected $_logger;

    const REVIEW_CACHE_KEY = 'dwd_kiyoh_product_reviews';

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

    /**
     * @param $productId int | string
     * @return string
     */
    protected function createCacheKey($productId)
    {
        return self::REVIEW_CACHE_KEY . "_" . $productId;
    }


    /**
     * @param $productId int | string
     * @return array
     */
    public function gatherReviews($productId)
    {
        $cache_key = $this->createCacheKey($productId);
        $reviews = $this->_cache->load($cache_key);
        if (!$reviews) {
            $locationId = $this->helperData->getKiyohLocationId();
            $url = $this->helperData->getKiyohReviewProductFeed() . '?locationId=' . $locationId . '&productCode=' . $productId;

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
                $this->_cache->save(serialize($reviews), $cache_key, array(), 3600);
            }
        } else {
            $reviews = unserialize($reviews);
        }
        return $reviews;
    }

    /**
     * @param $productId int | string
     * @return array
     */
    public function parseProductReviews($productId)
    {
        $reviewArray = json_decode($this->gatherReviews($productId), true);
        $productReviews = array();

        try {
            if (is_array($reviewArray) && isset($reviewArray["reviews"])) {
                foreach ($reviewArray["reviews"] as $productReviewsTemp) {
                    array_push(
                        $productReviews, array(
                            "reviewAuthor" => $productReviewsTemp["reviewAuthor"],
                            "rating" => $productReviewsTemp["rating"],
                            "title" => $productReviewsTemp["oneliner"],
                            "content" => $productReviewsTemp["description"],
                        )
                    );
                }
            }
        } catch (\Exception $e) {
            $this->_logger->customLog(
                'DWD Curl catch: ' . $e->getMessage()
            );
        }
        return $productReviews;
    }

    protected function getProductGatherRating($productId)
    {
        $rating = NULL;
        $reviewArray = json_decode($this->gatherReviews($productId), true);

        if (is_array($reviewArray) && isset($reviewArray["reviews"]) && !empty($reviewArray["reviews"])) {
            foreach ($reviewArray["reviews"] as $singleReviewArray) {
                $rating += $singleReviewArray["rating"];
            }
            $rating = ($rating / count($reviewArray["reviews"]));
        }
        return $rating;
    }

    protected function getProductGatherNumberReviews($productId)
    {
        $countReviews = 0;
        $reviewArray = json_decode($this->gatherReviews($productId), true);

        if (is_array($reviewArray) && isset($reviewArray["reviews"])) {
            $countReviews = count($reviewArray["reviews"]);
        }
        return $countReviews;
    }

    /**
     * @return array
     */
    public function getProductReviews()
    {
        $reviews = array();
        $product = $this->getProduct();
        if (isset($product) && !empty($product->getId()))
            $reviews = $this->parseProductReviews($product->getId());

        return $reviews;
    }

    /**
     * @return integer
     */
    public function getProductReviewCount($id = false)
    {
        $reviews = 0;
        if (!$id) {
            $id = $this->getProduct()->getId();
        }

        if ($id) {
            $reviews = $this->getProductGatherNumberReviews($id);
        }

        return $reviews;
    }

    /**
     * @return integer
     */
    public function getProductReviewRating($id = false)
    {
        $rating = NULL;
        if (!$id) {
            $id = $this->getProduct()->getId();
        }

        if ($id) {
            $rating = $this->getProductGatherRating($id);
        }

        return $rating;
    }


    public function getProduct()
    {
        return $this->_catalogHelper->getProduct();
    }


}
