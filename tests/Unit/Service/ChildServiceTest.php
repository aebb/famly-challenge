<?php

namespace App\Tests\Unit\Service;

use App\Entity\Child;
use App\Repository\RepositoryFactory;
use App\Repository\ChildRepository;
use App\Service\ChildService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionProperty;

/**
 * @coversDefaultClass \App\Service\ChildService
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ChildServiceTest extends TestCase
{
    private LoggerInterface $logger;
    private RepositoryFactory $repositoryFactory;
    private ChildRepository $childRepository;
    private int $limit;
    private ChildService $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->logger            = $this->createMock(LoggerInterface::class);
        $this->repositoryFactory = $this->createMock(RepositoryFactory::class);
        $this->childRepository   = $this->createMock(ChildRepository::class);
        $this->limit             = 5;
        $this->sut = new ChildService(
            $this->logger,
            $this->repositoryFactory,
            $this->limit
        );

        $this->repositoryFactory
            ->method('getChildRepository')
            ->willReturn($this->childRepository);
    }

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $sut = new ChildService(
            $this->logger,
            $this->repositoryFactory,
            $this->limit
        );

        $prop = new ReflectionProperty(ChildService::class, 'logger');
        $prop->setAccessible(true);
        $this->assertEquals($this->logger, $prop->getValue($sut));

        $prop = new ReflectionProperty(ChildService::class, 'repositoryFactory');
        $prop->setAccessible(true);
        $this->assertEquals($this->repositoryFactory, $prop->getValue($sut));

        $prop = new ReflectionProperty(ChildService::class, 'limit');
        $prop->setAccessible(true);
        $this->assertEquals($this->limit, $prop->getValue($sut));
    }

    /**
     * @covers::listChildrenByDuration
     */
    public function testListingDurationSuccess()
    {
        $start = 1;
        $count = 1;
        $duration = 1;

        $expected = [$this->createMock(Child::class)];

        $this->childRepository
            ->expects($this->once())
            ->method('findChildrenByAttendanceDuration')
            ->with($start, $count, $duration)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->sut->listChildrenByDuration($start, $count, $duration));
    }
}
