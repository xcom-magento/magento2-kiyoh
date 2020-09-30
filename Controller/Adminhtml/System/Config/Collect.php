<?php

namespace DWD\KiyohReviews\Controller\Adminhtml\System\Config;

use DWD\KiyohReviews\Helper\Data as HelperData;
use DWD\KiyohReviews\Helper\Logger;
use DWD\KiyohReviews\Model\Product\Products;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\ManagerInterface as EventManager;
use  \Magento\Framework\Controller\Result\JsonFactory;

class Collect extends Action
{
    protected $resultJsonFactory;

    protected $_request;

    protected $_productsModel;

    private $eventManager;

    protected $helperData;

    protected $helperLogger;

    public function __construct(
        Http $request,
        Context $context,
        Logger $helperLogger,
        HelperData $helperData,
        Products $_productsModel,
        EventManager $eventManager,
        JsonFactory $resultJsonFactory
    )
    {
        $this->_productsModel = $_productsModel;
        $this->eventManager = $eventManager;
        $this->helperData = $helperData;
        $this->helperLogger = $helperLogger;
        $this->_request = $request;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        try {
            $products = $this->_productsModel->getProductData();
            $this->eventManager->dispatch('dwd_kiyoh_send_products', ['products' => $products]);
            $this->helperLogger->customLog("Manual product sync run successfully");
            $response = ['success' => 'true'];
        } catch (\Exception $e) {
            $this->helperLogger->customLog(basename(__FILE__) . ' ' . $e->getMessage());
            $response = ['error' => 'true', 'message' => $e->getMessage()];
        }
        return $resultJson->setData($response);
    }

}
