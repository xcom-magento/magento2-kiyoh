<?php

namespace DWD\KiyohReviews\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\Framework\Event\Observer;
use DWD\KiyohReviews\Model\Product\Products;
use \Magento\Framework\App\State;
use Magento\Framework\Event\ManagerInterface as EventManager;

/**
 * Class KiyohProductsCommand
 */
class KiyohProductsCommand extends Command
{

    const PRODUCT_ID = 'productid';

    const LOG_ONLY = 'log';

    protected $_productsModel;

    protected $_observer;

    /** @var \Magento\Framework\App\State * */
    private $state;

    /**
     * @var EventManager
     */
    private $eventManager;


    public function __construct(
        State $state,
        Products $_productsModel,
        Observer $observer,
        EventManager $eventManager
    )
    {
        $this->state = $state;
        $this->_productsModel = $_productsModel;
        $this->_observer = $observer;
        $this->eventManager = $eventManager;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('dwd:kiyoh:send-products');
        $this->setDescription('give ID (int) or ALL also possible to log only data and not send');
        $this->addOption(
            self::PRODUCT_ID,
            null,
            InputOption::VALUE_REQUIRED,
            'int || string ALL'
        );
        $this->addOption(
            self::LOG_ONLY,
            null,
            InputOption::VALUE_REQUIRED,
            'true || false when true it will not send any data'
        );
        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $productID = $input->getOption(self::PRODUCT_ID);
        $log_only =  $input->getOption(self::LOG_ONLY);
        if ($productID) {
            if ($productID == "ALL") {
                $products = $this->_productsModel->getProductData();
            } else {
                $products = $this->_productsModel->getSingleProduct($productID);
            }
            if($log_only === "false"){
                $this->eventManager->dispatch('dwd_kiyoh_send_products', ['products' => $products]);
            }else{
                $output->writeln(print_r($products,true));
                $output->writeln("only logged not sended");
            }

        } else {
            $output->writeln('<error>Please provide --productid (id or ALL) and --log (true or false)</error>');
        }
    }
}
