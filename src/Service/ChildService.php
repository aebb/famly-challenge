<?php

namespace App\Service;

use App\Repository\RepositoryFactory;
use App\Request\Child\ListRequest;
use App\Utils\AbstractService;
use Psr\Log\LoggerInterface;

class ChildService extends AbstractService
{
    private int $limit;

    public function __construct(LoggerInterface $logger, RepositoryFactory $repositoryFactory, int $listLimit)
    {
        parent::__construct($logger, $repositoryFactory);
        $this->limit = $listLimit;
    }

    public function listChildrenCheckedIn(ListRequest $request): array
    {
        $this->logger->info(sprintf(self::LOG_MESSAGE_STARTED, $request->getRequest()->getBaseUrl()));

        $start   = $request->getStart() ?? 0;
        $count    = min($request->getCount() ?? $this->limit, $this->limit);

        return $this->repositoryFactory->getChildRepository()->findChildrenCurrentlyCheckedIn(
            $request->getSearch() ?? '',
            $start,
            $count
        );
    }

    public function listChildrenByDuration(?int $offset, ?int $limit, int $duration): array
    {
        $start   = $offset ?? 0;
        $count   = min($limit ?? $this->limit, $this->limit);

        return $this->repositoryFactory->getChildRepository()->findChildrenByAttendanceDuration(
            $start,
            $count,
            $duration
        );
    }
}
