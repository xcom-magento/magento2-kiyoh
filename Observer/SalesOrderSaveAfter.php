<?php

namespace DWD\KiyohReviews\Observer;

use DWD\KiyohReviews\Helper\Data as HelperData;
use DWD\KiyohReviews\Helper\Logger;
use DWD\KiyohReviews\Model\Send\ProductReviewSender;
use DWD\KiyohReviews\Model\Send\CompanyReviewSender;

class SalesOrderSaveAfter implements \Magento\Framework\Event\ObserverInterface
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
            if (isset($order) && $order->getState() === $this->helperData->getKiyohEventTrigger()) {
                $this->helperLogger->customLog(
                    'Send product review triggered on ' . $order->getState()
                );
                $this->reviewSender->sendReview($order, false);
            }
        }else{
            if ($this->helperData->getKiyohCompanyEnable()) {
                $order = $observer->getEvent()->getOrder();
                if (isset($order) && $order->getState() === $this->helperData->getKiyohEventTrigger()) {
                    $this->helperLogger->customLog(
                        'Send company review triggered on ' . $order->getState()
                    );
                    $this->companyReviewSender->sendReview($order, false);
                }
            }
        }
        return $this;
    }
}
