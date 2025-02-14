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

use \Magento\Cron\Model\Schedule;

/**
 * Class  Emoja\Scheduler\Model\JobScheduler
 *
 * @category    Emoja
 * @package     Emoja_Scheduler
 */
class JobScheduler extends \Magento\Framework\Model\AbstractModel
{

    /** @var  \Emoja\Scheduler\Model\ResourceModel\Job\Collection */
    protected $jobCollection;

    /** @var  \Emoja\Scheduler\Model\ResourceModel\Job\CollectionFactory */
    protected $jobCollectionFactory;

    /** @var \Magento\Cron\Model\ScheduleFactory */
    protected $scheduleFactory;

    /** @var \Magento\Framework\Stdlib\DateTime\DateTime */
    protected $dateTime;

    /** @var */
    protected $job;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Emoja\Scheduler\Model\ResourceModel\Job\CollectionFactory $jobCollectionFactory,
        \Magento\Cron\Model\ScheduleFactory $scheduleFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->jobCollectionFactory = $jobCollectionFactory;
        $this->scheduleFactory = $scheduleFactory;
        $this->dateTime = $dateTime;
    }


    public function getJobCollection()
    {
        if (empty($this->jobCollection)) {
            /** @var \Emoja\Scheduler\Model\ResourceModel\Job\Collection $jobCollection */
            $this->jobCollection = $this->jobCollectionFactory->create();
        }
        return $this->jobCollection;
    }

    /**
     * Generate tasks schedule
     *
     */
    public function scheduleJobs($jobCodes)
    {
        $this->getJobCollection()->load();

        $result = [];
        foreach ($jobCodes as $jobCode) {
            $jobConfig = $this->getJobCollection()->getItemById($jobCode);
            if ($jobConfig) {
                $cronExpr = !empty($jobConfig['schedule']) ? $jobConfig['schedule'] : '* * * * *';
                $schedule = $this->createSchedule($jobConfig['job_code'], $cronExpr);
                $schedule->save();
                $result[$jobCode] = $schedule;
            }
        }
        return $result;
    }

    public function createSchedule($jobCode, $cronExpression, $at = null, $data = null)
    {
        if (!isset($at)) {
            $at = $this->dateTime->gmtTimestamp();
        }
        $schedule = $this->scheduleFactory->create()
            ->setCronExpr($cronExpression)
            ->setJobCode($jobCode)
            ->setStatus(Schedule::STATUS_PENDING)
            ->setCreatedAt(date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()))
            ->setScheduledAt(date('Y-m-d H:i:s', $at))
            ->setAdditionalData($data);

        return $schedule;
    }

}
