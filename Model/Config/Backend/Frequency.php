<?php

namespace DWD\KiyohReviews\Model\Config\Backend;


use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Config value backend model.
 */
class Frequency extends \Magento\Framework\App\Config\Value
{
    /**
     * Cron string path
     */
    const CRON_SCHEDULE_PATH = 'crontab/default/jobs/kiyoh_feed_job/schedule/cron_expr';


    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param WriterInterface $configWriter
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        WriterInterface $configWriter,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->configWriter = $configWriter;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     * @throws \Exception
     */
    public function afterSave()
    {

        $time = $this->getData('groups/group_kiyoh_product/fields/dwd_kiyoh_cron_time/value');
        $frequency = $this->getData('groups/group_kiyoh_product/fields/dwd_kiyoh_cron_frequency/value');

        $cronExprArray = [
            intval($time[1]), //Minute
            intval($time[0]), //Hour
            $frequency == \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY ? '1' : '*', //Day of the Month
            '*', //Month of the Year
            $frequency == \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY ? '1' : '*', //Day of the Week
        ];


        $cronExprString = join(' ', $cronExprArray);

        try {
            $this->configWriter->save(self::CRON_SCHEDULE_PATH, $cronExprString);
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
            throw new LocalizedException(__('Cron settings can\'t be saved'));
        }


        return parent::afterSave();
    }
}
