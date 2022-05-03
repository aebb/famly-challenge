<?php

namespace App\Service;

use App\Entity\Attendance;
use App\Repository\RepositoryFactory;
use App\Request\Attendance\CreateRequest;
use App\Request\Attendance\UpdateRequest;
use App\Utils\AbstractService;
use App\Utils\AppException;
use App\Utils\ErrorCode;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class AttendanceService extends AbstractService
{
    public function __construct(LoggerInterface $logger, RepositoryFactory $repositoryFactory)
    {
        parent::__construct($logger, $repositoryFactory);
    }

    /**
     * @throws AppException
     */
    public function checkIn(CreateRequest $request): Attendance
    {
        $this->logger->info(sprintf(self::LOG_MESSAGE_STARTED, $request->getRequest()->getBaseUrl()));

        $childRepository = $this->repositoryFactory->getChildRepository();
        $child = $childRepository->findOneBy(['id' => $request->getId()]);

        if (!$child) {
            $this->logger->info(sprintf(self::LOG_MESSAGE_ERROR, $request->getRequest()));
            throw new AppException(
                ErrorCode::CHILD_NOT_FOUND,
                ErrorCode::ERROR_CODE_CHECK_IN,
                null,
                Response::HTTP_NOT_FOUND
            );
        }

        $attendanceRepository = $this->repositoryFactory->getAttendanceRepository();
        $attendance = $attendanceRepository->findAttendedByChild($child);

        if ($attendance) {
            $this->logger->info(sprintf(self::LOG_MESSAGE_ERROR, $request->getRequest()));
            throw new AppException(
                ErrorCode::CHILD_ALREADY_CHECKED_IN,
                ErrorCode::ERROR_CODE_CHECK_IN,
                null,
                Response::HTTP_CONFLICT
            );
        }

        return $attendanceRepository->persist(new Attendance($child));
    }

    /**
     * @throws AppException
     */
    public function checkOut(UpdateRequest $request): Attendance
    {
        $this->logger->info(sprintf(self::LOG_MESSAGE_STARTED, $request->getRequest()->getBaseUrl()));

        $repository = $this->repositoryFactory->getAttendanceRepository();
        $attendance = $repository->findOneBy(['id' => $request->getId()]);
        if (!$attendance) {
            $this->logger->info(sprintf(self::LOG_MESSAGE_ERROR, $request->getRequest()));
            throw new AppException(
                ErrorCode::ATTENDANCE_NOT_FOUND,
                ErrorCode::ERROR_CODE_CHECK_OUT,
                null,
                Response::HTTP_NOT_FOUND
            );
        }

        if ($attendance->getLeftAt()) {
            $this->logger->info(sprintf(self::LOG_MESSAGE_ERROR, $request->getRequest()));
            throw new AppException(
                ErrorCode::CHILD_ALREADY_CHECKED_OUT,
                ErrorCode::ERROR_CODE_CHECK_OUT,
                null,
                Response::HTTP_CONFLICT
            );
        }

        $attendance->setLeftAt(new DateTime());
        return $repository->update($attendance);
    }
}
