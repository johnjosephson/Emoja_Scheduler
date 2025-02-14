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
class MassRun extends \Magento\Backend\App\Action
{
    /** @var \Emoja\Scheduler\Model\JobRunner */
    protected $jobRunner;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Emoja\Scheduler\Model\JobRunnerFactory $jobRunnerFactory
    )
    {
        parent::__construct($context);
        $this->jobRunner = $jobRunnerFactory->create();
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $jobCodes = $this->getRequest()->getParam('job_codes');
        if (!is_array($jobCodes)) $jobCodes = array($jobCodes);

        foreach($jobCodes as $jobCode) {
            try {
                $this->jobRunner->runJob($jobCode);
                $message = __('job %1 run successfully', $jobCode);
                $this->getMessageManager()->addSuccessMessage($message);
            } catch (\Throwable $e) {
                $message = __('Error running job %1: %2', $jobCode, $e->getMessage());
                $this->getMessageManager()->addErrorMessage($message);
                break;
            }
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
