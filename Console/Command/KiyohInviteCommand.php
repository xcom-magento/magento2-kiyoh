<?php

namespace DWD\KiyohReviews\Console\Command;

use Magento\Framework\Event\ManagerInterface as EventManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DWD\KiyohReviews\Model\DataOrderCollect;
use Magento\Framework\Event\Observer;
use \Magento\Framework\App\State;


/**
 * Class KiyohInviteCommand
 */
class KiyohInviteCommand extends Command
{

    const ORDER_ID = 'orderid';

    const TEST_MODE = 'testmode';

    protected $_sendReviewModel;

    protected $_observer;

    /** @var \Magento\Framework\App\State * */
    private $state;

    private $eventManager;

    public function __construct(
        State $state,
        DataOrderCollect $sendReviewModel,
        Observer $observer,
        EventManager $eventManager
    )
    {
        $this->state = $state;
        $this->_sendReviewModel = $sendReviewModel;
        $this->_observer = $observer;
        $this->eventManager = $eventManager;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('dwd:kiyoh:invite');
        $this->setDescription('send kiyoh review to corresponding order');
        $this->addOption(
            self::ORDER_ID,
            null,
            InputOption::VALUE_REQUIRED,
            'takes order id'
        );
        $this->addOption(
            self::TEST_MODE,
            null,
            InputOption::VALUE_REQUIRED,
            'takes true or false'
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

        $orderId = $input->getOption(self::ORDER_ID);
        $testMode = $input->getOption(self::TEST_MODE);

        if ($testMode == 'false' && $orderId) {
            $order = $this->_sendReviewModel->getOrderById($orderId);
            $this->eventManager->dispatch('dwd_kiyoh_send_invite', ['order' => $order, 'testmode' => false]);
            $output->writeln('<info>Provided orderId is `' . $orderId . '`</info>');
        } elseif ($testMode == 'true' && $orderId) {
            $order = $this->_sendReviewModel->getOrderById($orderId);
            $this->eventManager->dispatch('dwd_kiyoh_send_invite', ['order' => $order, 'testmode' => true]);
            $output->writeln('<info>test mode on</info>');
            $output->writeln('<info>Provided orderId is `' . $orderId . '`</info>');
        } else {
            $output->writeln('<error>Please provide an orderId</error>');
        }
    }
}
