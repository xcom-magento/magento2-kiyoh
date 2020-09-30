<?php

namespace DWD\KiyohReviews\Observer;

use DWD\KiyohReviews\Helper\Data as HelperData;
use DWD\KiyohReviews\Helper\Logger;
use DWD\KiyohReviews\Model\Send\ProductReviewSender;
use DWD\KiyohReviews\Model\Send\CompanyReviewSender;

class SendCustomTriggerEvent implements \Magento\Framework\Event\ObserverInterface
{
    protected $helperLogger;

    protected $helperData;

    protected $reviewSender;

    protected $companyReviewSender;

    public function __construct(
        HelperData $helperData,
        Logger $helperLogger,
        ProductReviewSender $reviewSender,
        CompanyReviewSender $companyReviewSender
    )
    {
        $this->companyReviewSender = $companyReviewSender;
        $this->reviewSender = $reviewSender;
        $this->helperData = $helperData;
        $this->helperLogger = $helperLogger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helperData->getKiyohProductEnabled()) {
            $order = $observer->getEvent()->getOrder();
            if (isset($order)) {
                $this->helperLogger->customLog(
                    'Send Company & Product review triggered on custom kiyoh event'
                );
                $testMode = $observer->getEvent()->getTestmode();
                $this->reviewSender->sendReview($order, $testMode);
            } else {
                $this->helperLogger->customLog(
                    basename(__FILE__) . ' ' . 'DWD Company & Product Observer error: no order input'
                );
            }
        } else {
            if ($this->helperData->getKiyohCompanyEnable()) {
                $order = $observer->getEvent()->getOrder();
                if (isset($order)) {
                    $this->helperLogger->customLog(
                        'Send Company review triggered on custom kiyoh event'
                    );
                    $testMode = $observer->getEvent()->getTestmode();
                    $this->companyReviewSender->sendReview($order, $testMode);
                } else {
                    $this->helperLogger->customLog(
                        basename(__FILE__) . ' ' . 'DWD Company Observer error: no order input'
                    );
                }
            }
        }
        return $this;
    }
}
