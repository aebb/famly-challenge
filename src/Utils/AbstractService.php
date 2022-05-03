<?php

namespace App\Utils;

use App\Repository\RepositoryFactory;
use Psr\Log\LoggerInterface;

abstract class AbstractService
{
    public const LOG_MESSAGE_STARTED = 'STARTED REQUEST: %s';
    public const LOG_MESSAGE_FINISH  = 'COMPLETED REQUEST: %s';
    public const LOG_MESSAGE_QUERY   = 'RETRIEVED FROM DB %s';
    public const LOG_MESSAGE_ERROR   = 'ERROR REQUEST %s';

    protected LoggerInterface $logger;

    protected RepositoryFactory $repositoryFactory;

    public function __construct(LoggerInterface $logger, RepositoryFactory $repositoryFactory)
    {
        $this->logger = $logger;
        $this->repositoryFactory = $repositoryFactory;
    }
}
