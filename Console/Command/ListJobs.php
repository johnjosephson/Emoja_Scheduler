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

class ListJobs extends Command
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
        $this->setName('emoja:scheduler:listjobs')
            ->setDescription('List cron jobs by job code')
            ->addArgument(
                self::JOB_CODE_ARGUMENT,
                InputArgument::OPTIONAL,
                'Job code match',
                ''
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
        $matchCode = $input->getArgument(self::JOB_CODE_ARGUMENT);;
        $regex = '/.*' . $matchCode . '.*/i';
        $output->writeln('Listing jobs: ');
        $jobCodes = $this->jobRunner->getJobCodes();
        foreach ($jobCodes as $code) {
            if (preg_match($regex, $code)) {
                $output->writeln($code);
            }
        }
    }
}
