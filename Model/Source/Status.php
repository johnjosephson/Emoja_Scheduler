<?php
/**
 * Emoja_Scheduler
 *
 * @category    Emoja
 * @package     Emoja_Scheduler
 * @copyright   Copyright (c) 2025 Emoja Consulting, Inc
 * @author      johnjay@alumni.caltech.edu
 */

namespace Emoja\Scheduler\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Cron\Model\Schedule;

/**
 * Class  Emoja\Scheduler\Model\Source\Status
 *
 * @category    Emoja
 * @package     Emoja_Scheduler
 */
class Status implements ArrayInterface
{
    /**
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            '' => 'All',
            Schedule::STATUS_ERROR => 'Error',
            Schedule::STATUS_MISSED => 'Missed',
            Schedule::STATUS_PENDING => 'Pending',
            Schedule::STATUS_RUNNING => 'Running',
            Schedule::STATUS_SUCCESS => 'Success',
        ];
    }

    public function getName($type)
    {
        foreach ($this->toOptionArray() as $optionType => $name) {
            if ($optionType == $type) {
                return $name;
            }
        }
        return '';
    }
}
