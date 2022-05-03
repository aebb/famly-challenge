<?php

namespace App\Tests\Unit\Utils;

use App\Repository\RepositoryFactory;
use App\Service\AttendanceService;
use App\Utils\AbstractService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionProperty;

/**
 * @coversDefaultClass \App\Utils\AbstractService
 */
class AbstractServiceTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $repository = $this->createMock(RepositoryFactory::class);

        $sut = new AttendanceService($logger, $repository);

        $prop = new ReflectionProperty(AbstractService::class, 'logger');
        $prop->setAccessible(true);
        $this->assertEquals($logger, $prop->getValue($sut));

        $prop = new ReflectionProperty(AbstractService::class, 'repositoryFactory');
        $prop->setAccessible(true);
        $this->assertEquals($repository, $prop->getValue($sut));
    }
}
