<?php

namespace App\Repository;

use App\Entity\Attendance;
use App\Entity\Child;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class RepositoryFactory
{
    protected const REPOSITORY_USER       = 'user-repository';
    protected const REPOSITORY_CHILD      = 'child-repository';
    protected const REPOSITORY_ATTENDANCE = 'attendance-repository';

    protected ManagerRegistry $registry;
    protected EntityManagerInterface $entityManager;

    protected array $repositories;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->registry = $registry;
        $this->entityManager = $entityManager;
    }

    /** @codeCoverageIgnore */
    protected function createRepository(
        string $repository,
        ManagerRegistry $registry,
        EntityManagerInterface $entityManager,
        ...$args
    ) {
        return new $repository($registry, $entityManager, ...$args);
    }

    public function getUserRepository(): UserRepository
    {
        if (!isset($this->repositories[self::REPOSITORY_USER])) {
            $this->repositories[self::REPOSITORY_USER] = $this->createRepository(
                UserRepository::class,
                $this->registry,
                $this->entityManager
            );
        }
        return $this->repositories[self::REPOSITORY_USER];
    }

    public function getChildRepository(): ChildRepository
    {
        if (!isset($this->repositories[self::REPOSITORY_CHILD])) {
            $this->repositories[self::REPOSITORY_CHILD] = $this->createRepository(
                ChildRepository::class,
                $this->registry,
                $this->entityManager
            );
        }
        return $this->repositories[self::REPOSITORY_CHILD];
    }

    public function getAttendanceRepository(): AttendanceRepository
    {
        if (!isset($this->repositories[self::REPOSITORY_ATTENDANCE])) {
            $this->repositories[self::REPOSITORY_ATTENDANCE] = $this->createRepository(
                AttendanceRepository::class,
                $this->registry,
                $this->entityManager
            );
        }
        return $this->repositories[self::REPOSITORY_ATTENDANCE];
    }
}
