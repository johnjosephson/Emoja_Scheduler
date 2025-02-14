<?php
/**
 * Emoja_Scheduler
 *
 * @category    Emoja
 * @package     Emoja_Scheduler
 * @copyright   Copyright (c) 2025 Emoja Consulting, Inc
 * @author      johnjay@alumni.caltech.edu
 */

namespace Emoja\Scheduler\Model;

use Magento\Cron\Model\Schedule;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Cron\Model\ConfigInterface;
use \Magento\Framework\Lock\LockManagerInterface;
use \Magento\Framework\App\AreaList;
use \Psr\Log\LoggerInterface;
use \Exception;
use \Throwable;

/**
 * The observer for processing cron jobs.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class JobRunner
{
    const SECONDS_IN_MINUTE = 60;
    /**
     * @var State
     */
    protected $state;

    /**
     * @var AreaList
     */
    private $areaList;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var JobScheduler
     */
    protected $jobScheduler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Lock\LockManagerInterface
     */
    private $lockManager;

    /**
     * JobRunner constructor.
     * @param ObjectManagerInterface $objectManager
     * @param JobScheduler $jobScheduler
     * @param ConfigInterface $config
     * @param LoggerInterface $logger
     * @param LockManagerInterface $lockManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        State $state,
        AreaList $areaList,
        JobScheduler $jobScheduler,
        ConfigInterface $config,
        LoggerInterface $logger,
        LockManagerInterface $lockManager
    ) {
        $this->objectManager = $objectManager;
        $this->state = $state;
        $this->areaList = $areaList;
        $this->jobScheduler = $jobScheduler;
        $this->config = $config;
        $this->logger = $logger;
        $this->lockManager = $lockManager;
    }

    /**
     * @param $jobCode
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws Throwable
     */
    public function runJob($jobCode)
    {
        try {
            $this->state->getAreaCode();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);
        }

        $configLoader = $this->objectManager->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
        $this->objectManager->configure($configLoader->load(Area::AREA_CRONTAB));

        $this->areaList->getArea(Area::AREA_CRONTAB)->load(Area::PART_TRANSLATE);

        /** @var  $jobConfig array */
        $jobConfig = $this->getJob($jobCode);
        if ($jobConfig === false) {
            throw new Exception(sprintf('Invalid job code: %s', $jobCode));
        }

        $schedule = $this->jobScheduler->createSchedule($jobCode, '* * * * *');
        return $this->_runJob($jobCode, $jobConfig, $schedule);
    }

    /**
     * @return array
     */
    public function getJobCodes()
    {
        $jobCodes = [];
        $jobGroupsRoot =  $this->config->getJobs();
        foreach ($jobGroupsRoot as $groupId => $jobGroup) {
            foreach ($jobGroup as $code => $jobConfig) {
                $jobCodes[] = $code;
            }
        }
        return $jobCodes;
    }

    /**
     * @return array|bool
     */
    protected function getJob($jobCode)
    {
        $jobGroupsRoot = $this->config->getJobs();
        foreach ($jobGroupsRoot as $groupId => $jobGroup) {
            foreach ($jobGroup as $code => $jobConfig) {
                if ($jobCode == $code) {
                    return $jobConfig;
                }
            }
        }
        return false;
    }

    /**
     * @param $jobCode
     * @param $jobConfig
     * @param $schedule
     * @return int
     * @throws Throwable
     */
    protected function _runJob($jobCode, $jobConfig, $schedule)
    {
        if (!isset($jobConfig['instance'], $jobConfig['method'])) {
            $schedule->setStatus(Schedule::STATUS_ERROR);
            // phpcs:ignore Magento2.Exceptions.DirectThrow
            throw new Exception(sprintf('No callbacks found for cron job %s', $jobCode));
        }
        $model = $this->objectManager->create($jobConfig['instance']);
        $callback = [$model, $jobConfig['method']];
        if (!is_callable($callback)) {
            // phpcs:ignore Magento2.Exceptions.DirectThrow
            throw new Exception(
                sprintf('Invalid callback: %s::%s can\'t be called', $jobConfig['instance'], $jobConfig['method'])
            );
        }
        try {
            $this->logger->info(sprintf('Cron Job %s is run', $jobCode));
            //phpcs:ignore Magento2.Functions.DiscouragedFunction
            call_user_func_array($callback, [$schedule]);
        } catch (Throwable $e) {
            $this->logger->error(
                sprintf(
                    'Cron Job %s has an error: %s.',
                    $jobCode,
                    $e->getMessage()
                 )
            );
            if (!$e instanceof Exception) {
                $e = new \RuntimeException(
                    'Error when running a cron job',
                    0,
                    $e
                );
            }
            throw $e;
        }

        $this->logger->info(
            sprintf(
                'Cron Job %s is successfully finished.',
                $jobCode
            )
        );
        return 1;
    }


}
