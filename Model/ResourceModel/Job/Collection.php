<?php
/**
 * Emoja_Scheduler
 *
 * @category    Emoja
 * @package     Emoja_Scheduler
 * @copyright   Copyright (c) 2025 Emoja Consulting, Inc
 * @author      johnjay@alumni.caltech.edu
 */

namespace Emoja\Scheduler\Model\ResourceModel\Job;

use Magento\Framework\Data\Collection\EntityFactoryInterface;

/**
 * Class  Emoja\Scheduler\Model\ResourceModel\Job\Collection
 *
 * @category    Emoja
 * @package     Emoja_Scheduler
 */
class Collection extends \Magento\Framework\Data\Collection
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Cron\Model\ConfigInterface
     */
    protected $_config;

    /**
     * @param EntityFactoryInterface $entityFactory
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        \Magento\Cron\Model\ConfigInterface $config,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($entityFactory);
        $this->_config = $config;
        $this->_scopeConfig = $scopeConfig;
    }


    /**
     * Load data
     *
     * @param   bool $printQuery
     * @param   bool $logQuery
     * @return  $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $jobGroupsRoot = $this->_config->getJobs();
        // group => [ code => [ name, instance, method, schedule ] ]
        foreach ($jobGroupsRoot as $groupId => $job) {
            foreach ($job as $jobCode => $jobConfig) {
                $varienObject = new \Magento\Framework\DataObject();
                $jobConfig['schedule'] = $this->getCronExpression($jobConfig);
                $jobConfig['group'] = $groupId;
                $jobConfig['job_code'] = $jobCode;
                $jobConfig['full_method'] = $jobConfig['instance'] . '::' . $jobConfig['method'];
                $varienObject->setData($jobConfig);
                try {
                    $this->addItem($varienObject);
                } catch (\Exception $e) {
                    // TODO : LOG ME
                }
            }
        }
        ksort($this->_items);
        $this->_setIsLoaded(true);
        return $this;
    }

    protected function _getItemId(\Magento\Framework\DataObject $item)
    {
        return $item->getData('job_code');
    }

    /**
     * @param array $jobConfig
     * @return null|string
     */
    private function getCronExpression($jobConfig)
    {
        $cronExpression = null;
        if (isset($jobConfig['config_path'])) {
            $cronExpression = $this->getConfigSchedule($jobConfig) ?: null;
        }

        if (!$cronExpression) {
            if (isset($jobConfig['schedule'])) {
                $cronExpression = $jobConfig['schedule'];
            }
        }
        return $cronExpression;
    }


    /**
     * @param array $jobConfig
     * @return mixed
     */
    protected function getConfigSchedule($jobConfig)
    {
        $cronExpr = $this->_scopeConfig->getValue(
            $jobConfig['config_path'],
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $cronExpr;
    }

}
