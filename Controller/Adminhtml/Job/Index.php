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

/**
 * Class Emoja\Scheduler\Controller\Adminhtml\Job\Index
 *
 * @category    Emoja
 * @package     Emoja_Scheduler
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $this->updateMenu($resultPage);
        return $resultPage;
    }

    /**
     * Check if user has enough privileges
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Emoja_Scheduler::job');
    }

    /**
     * @param $resultPage \Magento\Backend\Model\View\Result\Page
     */
    protected function updateMenu($resultPage)
    {
        $resultPage->setActiveMenu('Emoja_Scheduler::job');
        $resultPage->addBreadcrumb(__('Job'), __('Job'));
        $resultPage->addBreadcrumb(__('Scheduler'), __('Scheduler'));
        $resultPage->getConfig()->getTitle()->prepend(__('Cron Jobs'));

    }
}
