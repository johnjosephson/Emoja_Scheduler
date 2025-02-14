<?php
/**
 * Emoja_Scheduler
 *
 * @category    Emoja
 * @package     Emoja_Scheduler
 * @copyright   Copyright (c) 2025 Emoja Consulting, Inc
 * @author      johnjay@alumni.caltech.edu
 */

namespace Emoja\Scheduler\Controller\Adminhtml\Job;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Emoja\Scheduler\Controller\Adminhtml\Job\MassSchedule
 *
 * @category    Emoja
 * @package     Emoja_Scheduler
 */
class MassSchedule extends \Magento\Backend\App\Action
{
    /** @var \Emoja\Scheduler\Model\JobScheduler */
    protected $jobScheduler;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Emoja\Scheduler\Model\JobSchedulerFactory $jobSchedulerFactory
    )
    {
        parent::__construct($context);
        $this->jobScheduler = $jobSchedulerFactory->create();
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $jobCodes = $this->getRequest()->getParam('job_codes');
        if (!is_array($jobCodes)) $jobCodes = array($jobCodes);

        $results = $this->jobScheduler->scheduleJobs($jobCodes);
        foreach ($jobCodes as $jobCode) {
            if (isset($results[$jobCode])) {
                /** @var \Magento\Cron\Model\Schedule $schedule */
                $schedule = $results[$jobCode];
                $message = __('%1 scheduled successfully for %2 (schedule_id=%3)', $schedule->getJobCode(), $schedule->getScheduledAt(), $schedule->getId());
                $this->getMessageManager()->addSuccess($message);
            } else {
                $message = __('%1 scheduled failed.', $jobCode);
                $this->getMessageManager()->addError($message);
            }
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
