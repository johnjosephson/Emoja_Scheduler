<?php
/**
 * Emoja_ScheduleJob
 *
 * @copyright   Copyright (c) 2022 Emoja Consulting, Inc.
 * @author      johnjay@alumni.caltech.edu
 */

namespace Emoja\Scheduler\Console\Command;

use Emoja\Scheduler\Model\JobRunner;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunJob extends Command
{
    const JOB_CODE_ARGUMENT = 'job_code';

    /** @var State */
    protected $state;

    /** @var JobRunner */
    private $jobRunner;

    public function __construct(
        State $state,
        JobRunner $jobRunner
    ) {
        $this->jobRunner = $jobRunner;
        $this->state = $state;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('emoja:scheduler:run')
            ->setDescription('Run a cron job by job code')
            ->addArgument(
                self::JOB_CODE_ARGUMENT,
                InputArgument::REQUIRED,
                'Job code to run'
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
        $jobCode = $input->getArgument(self::JOB_CODE_ARGUMENT);
        $output->writeln('Running job: ' . $jobCode);
        $this->jobRunner->runJob($jobCode);
        $output->writeln('Job: ' . $jobCode . ' complete.');
    }
}
