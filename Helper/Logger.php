<?php

namespace DWD\KiyohReviews\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use DWD\KiyohReviews\Helper\Data as HelperData;

Class Logger extends AbstractHelper
{
    const LOG_PATH = '/var/log/dwd_kiyoh_product.log';

    protected $helperData;

    public function __construct(
        HelperData $helperData,
        Context $context
    )
    {
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    public function customLog($message){
        if($this->helperData->getKiyohDebugEnable()){
            $writer = new \Zend\Log\Writer\Stream(BP . self::LOG_PATH);
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($message);
        }
    }

}
