<?php

namespace App\Tests\Unit\Repository;

use App\Repository\AttendanceRepository;
use App\Repository\ChildRepository;
use App\Repository\RepositoryFactory;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \App\Repository\RepositoryFactory
*/
class RepositoryFactoryTest extends TestCase
{
    protected ManagerRegistry $registry;
    protected EntityManagerInterface $entityManager;
    protected RepositoryFactory $sut;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->sut = $this->getMockBuilder(RepositoryFactory::class)
            ->setConstructorArgs([
                $this->registry,
                $this->entityManager,
            ])
            ->onlyMethods(['createRepository'])
            ->getMock();
    }


    /**
     * @covers ::__construct
     * @covers ::createRepository
     * @covers ::getAttendanceRepository
     */
    public function testAttendanceRepository()
    {
        $repository = $this->createMock(AttendanceRepository::class);

        $this->sut
            ->expects($this->once())
            ->method('createRepository')
            ->with(AttendanceRepository::class, $this->registry, $this->entityManager)
            ->willReturn($repository);

        $this->assertSame($repository, $this->sut->getAttendanceRepository());
    }

    /**
     * @covers ::__construct
     * @covers ::createRepository
     * @covers ::getChildRepository
     */
    public function testChildRepository()
    {
        $repository = $this->createMock(ChildRepository::class);

        $this->sut
            ->expects($this->once())
            ->method('createRepository')
            ->with(ChildRepository::class, $this->registry, $this->entityManager)
            ->willReturn($repository);

        $this->assertSame($repository, $this->sut->getChildRepository());
    }

    /**
     * @covers ::__construct
     * @covers ::createRepository
     * @covers ::getUserRepository
     */
    public function testUserRepository()
    {
        $repository = $this->createMock(UserRepository::class);

        $this->sut
            ->expects($this->once())
            ->method('createRepository')
            ->with(UserRepository::class, $this->registry, $this->entityManager)
            ->willReturn($repository);

        $this->assertSame($repository, $this->sut->getUserRepository());
    }
}
