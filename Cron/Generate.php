<?php

namespace DWD\KiyohReviews\Cron;

use DWD\KiyohReviews\Helper\Data as HelperData;
use \DWD\KiyohReviews\Helper\Logger;
use DWD\KiyohReviews\Model\Product\Products;
use Magento\Framework\Event\ManagerInterface as EventManager;

class Generate
{

    protected $_productsModel;

    private $eventManager;

    protected $helperData;

    protected $helperLogger;

    public function __construct(
        Logger $helperLogger,
        HelperData $helperData,
        Products $_productsModel,
        EventManager $eventManager
    ){
        $this->_productsModel = $_productsModel;
        $this->eventManager = $eventManager;
        $this->helperData = $helperData;
        $this->helperLogger = $helperLogger;
    }

    public function execute()
    {
        try {
            if($this->helperData->getKiyohCronEnable()){
                $products = $this->_productsModel->getProductData();
                $this->eventManager->dispatch('dwd_kiyoh_send_products', ['products' => $products]);
                $this->helperLogger->customLog("Cron product run successfully");
            }
        } catch (\Exception $e) {
            $this->helperLogger->customLog(basename(__FILE__) . ' ' . $e->getMessage());
        }
        return $this;
    }
}
