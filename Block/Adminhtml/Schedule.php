<?php
/**
 * Emoja_Scheduler
 *
 * @category    Emoja
 * @package     Emoja_Scheduler
 * @copyright   Copyright (c) 2025 Emoja Consulting, Inc
 * @author      johnjay@alumni.caltech.edu
 */

namespace Emoja\Scheduler\Block\Adminhtml;

/**
 * Class Emoja\Scheduler\Block\Adminhtml\Schedule
 *
 * @category    Emoja
 * @package     Emoja_Scheduler
 */
class Schedule extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_schedule';
        $this->_blockGroup = 'Emoja_Scheduler';
        $this->_headerText = __('Schedule Listing');

        parent::_construct();
        $this->removeButton('add');
    }
}
