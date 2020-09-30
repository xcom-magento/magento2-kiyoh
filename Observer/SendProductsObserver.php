<?php

namespace DWD\KiyohReviews\Observer;

use DWD\KiyohReviews\Helper\Data as HelperData;
use DWD\KiyohReviews\Helper\Logger;
use DWD\KiyohReviews\Model\Send\ProductsSender;

class SendProductsObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $helperData;

    protected $helperLogger;

    protected $productSender;

    public function __construct(
        HelperData $helperData,
        Logger $helperLogger,
        ProductsSender $productSender
    )
    {
        $this->productSender = $productSender;
        $this->helperData = $helperData;
        $this->helperLogger = $helperLogger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helperData->getKiyohProductEnabled()) {
            $productsData = $observer->getData('products');
            if (isset($productsData)) {
                $this->helperLogger->customLog(
                    'Send products triggered on custom kiyoh event'
                );
                $this->productSender->sendProducts($productsData);
            } else {
                $this->helperLogger->customLog(
                    basename(__FILE__) . ' ' . 'DWD Observer error: no product data input'
                );
            }
        }
        return $this;
    }
}
