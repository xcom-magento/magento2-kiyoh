<?php

namespace DWD\KiyohReviews\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 */
class Data extends AbstractHelper
{
    /**
     * How many products send at once to the api. 200 is max
     */
    const KIYOH_INPUT_MAX_SEND_PRODUCTS = 100;

    /**
     * How many products customer has to review of his order
     */
    const KIYOH_INPUT_MAX_INVITE_PRODUCT = 5;

    // https://www.kiyoh.com/v1/publication/review/external/location/statistics?locationId=1063244
    const KIYOH_COMPANY_REVIEW_URL = "/v1/publication/review/external/location/statistics";

    /**
     * POST BULK product  api
     */
    const KIYOH_INPUT_PRODUCT_BULK_URL = "/v1/location/product/external/bulk";

    /**
     * GET by id products api
     */
    const KIYOH_PRODUCT_REVIEW_URL = "/v1/publication/product/review/external";

    /**
     * POST invite
     */
    const KIYOH_INVITE_URL = "/v1/invite/external";

    /**
     * @var ScopeConfigInterface
     */
    protected $configScopeConfigInterface;

    public function __construct(
        ScopeConfigInterface $configScopeConfigInterface,
        Context $context
    )
    {
        $this->configScopeConfigInterface = $configScopeConfigInterface;
        parent::__construct($context);
    }

    public function getKiyohInputMaxSendProducts()
    {
        return self::KIYOH_INPUT_MAX_SEND_PRODUCTS;
    }

    public function getKiyohInputProductBulkUrl()
    {
        return $this->getKiyohServer() . self::KIYOH_INPUT_PRODUCT_BULK_URL;
    }

    public function getKiyohReviewProductFeed()
    {
        return $this->getKiyohServer() . self::KIYOH_PRODUCT_REVIEW_URL;
    }


    public function getKiyohReviewCompanyFeed()
    {
        return $this->getKiyohServer() . self::KIYOH_COMPANY_REVIEW_URL;
    }

    public function getKiyohInviteUrl()
    {
        return $this->getKiyohServer() . self::KIYOH_INVITE_URL;
    }

    /**
     * General
     */
    public function getKiyohApiKey()
    {
        return $this->configScopeConfigInterface->getValue(
            'dwd_kiyoh_product_reviews_section/general/dwd_kiyoh_product_api_key');
    }

    public function getKiyohLocationId()
    {
        return $this->configScopeConfigInterface->getValue(
            'dwd_kiyoh_product_reviews_section/general/dwd_kiyoh_product_location_id');
    }

    public function getKiyohServer()
    {
        return $this->configScopeConfigInterface->getValue(
            'dwd_kiyoh_product_reviews_section/general/dwd_kiyoh_server');
    }

    public function getKiyohEmailLang($storeId)
    {
        return $this->configScopeConfigInterface->getValue(
            'dwd_kiyoh_product_reviews_section/general/dwd_kiyoh_review_email_lang',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES,
            $storeId);
    }

    public function getKiyohEventTrigger()
    {
        return $this->configScopeConfigInterface->getValue(
            'dwd_kiyoh_product_reviews_section/general/dwd_kiyoh_event');
    }

    public function getKiyohDelay()
    {
        return $this->configScopeConfigInterface->getValue(
            'dwd_kiyoh_product_reviews_section/general/dwd_kiyoh_delay');
    }

    public function getKiyohDebugEnable()
    {
        return $this->configScopeConfigInterface->getValue(
            'dwd_kiyoh_product_reviews_section/general/dwd_kiyoh_log_enable');
    }
    /**
     * group_kiyoh_company
     */
    public function getKiyohCompanyEnable()
    {
        return $this->configScopeConfigInterface->getValue(
            'dwd_kiyoh_product_reviews_section/group_kiyoh_company/dwd_kiyoh_company_enable');
    }

    public function getKiyohCompanyWidgetEnable()
    {
        return $this->configScopeConfigInterface->getValue(
            'dwd_kiyoh_product_reviews_section/group_kiyoh_company/dwd_kiyoh_company_widget_enable');
    }
    /**
     * group_kiyoh_product
     */
    public function getKiyohProductEnabled()
    {
        return $this->configScopeConfigInterface->getValue(
            'dwd_kiyoh_product_reviews_section/group_kiyoh_product/dwd_kiyoh_product_enable');
    }

    public function getKiyohInputMaxInviteProducts()
    {
        return $this->configScopeConfigInterface->getValue(
            'dwd_kiyoh_product_reviews_section/group_kiyoh_product/dwd_kiyoh_product_max');
    }


    public function getKiyohCronEnable()
    {
        return $this->configScopeConfigInterface->getValue(
            'dwd_kiyoh_product_reviews_section/group_kiyoh_product/dwd_kiyoh_cron_enable');
    }

}

?>
