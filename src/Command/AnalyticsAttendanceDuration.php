<?php

namespace App\Command;

use App\Service\ChildService;
use App\Utils\AbstractService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:attendance:duration',
    description: 'Attendance duration analytics',
    hidden: false,
)]
class AnalyticsAttendanceDuration extends Command
{
    private const DURATION_ARG = 'duration';
    private const DURATION_DESCRIPTION = 'attendance duration';
    private const DURATION_DEFAULT = 120;

    private ChildService $service;

    private LoggerInterface $logger;

    public function __construct(ChildService $service, LoggerInterface $logger)
    {
        parent::__construct();
        $this->service = $service;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this->addArgument(
            self::DURATION_ARG,
            InputArgument::OPTIONAL,
            self::DURATION_DESCRIPTION,
            self::DURATION_DEFAULT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info(sprintf(AbstractService::LOG_MESSAGE_STARTED, self::class));

        $duration = ctype_digit($input->getArgument(self::DURATION_ARG)) ?
            intval($input->getArgument(self::DURATION_ARG)) :
            self::DURATION_DEFAULT;

        $output->writeln(
            $this->service->listChildrenByDuration(
                0,
                PHP_INT_MAX,
                $duration
            )
        );

        $this->logger->info(sprintf(AbstractService::LOG_MESSAGE_FINISH, self::class));

        return Command::SUCCESS;
    }
}
