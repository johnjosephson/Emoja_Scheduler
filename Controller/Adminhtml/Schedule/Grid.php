<?php
/**
 * Emoja_Scheduler
 *
 * @category    Emoja
 * @package     Emoja_Scheduler
 * @copyright   Copyright (c) 2025 Emoja Consulting, Inc
 * @author      johnjay@alumni.caltech.edu
 */

namespace Emoja\Scheduler\Controller\Adminhtml\Schedule;

/**
 * Class Emoja\Scheduler\Controller\Adminhtml\Schedule\Grid
 *
 * @category    Emoja
 * @package     Emoja_Scheduler
 */
class Grid extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_LAYOUT);
        return $resultPage;
    }

    /**
     * Check if user has enough privileges
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Emoja_Scheduler::schedule');
    }
}
