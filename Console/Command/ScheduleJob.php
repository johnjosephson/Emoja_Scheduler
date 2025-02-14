<?php
/**
 * Emoja_ScheduleJob
 *
 * @copyright   Copyright (c) 2022 Emoja Consulting, Inc.
 * @author      johnjay@alumni.caltech.edu
 */

namespace Emoja\Scheduler\Console\Command;

use Emoja\Scheduler\Model\JobScheduler;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScheduleJob extends Command
{
    const JOB_CODE_ARGUMENT = 'job_code';

    /** @var State */
    protected $state;

    /** @var JobScheduler */
    private $jobScheduler;

    public function __construct(
        State $state,
        JobScheduler $jobScheduler
    ) {
        $this->jobScheduler = $jobScheduler;
        $this->state = $state;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('emoja:scheduler:schedule')
            ->setDescription('Schedule a cron job by job code')
            ->addArgument(
                self::JOB_CODE_ARGUMENT,
                InputArgument::IS_ARRAY,
                'Job code to schedule'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);
        $jobCodes = $input->getArgument(self::JOB_CODE_ARGUMENT);
        $output->writeln('Scheduling jobs: ' . implode(', ' , $jobCodes));
        $this->jobScheduler->scheduleJobs($jobCodes);
    }
}
