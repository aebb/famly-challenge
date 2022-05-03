<?php

namespace App\Tests\Integration\Command;

use App\Entity\Child;
use App\Service\ChildService;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \App\Command\AnalyticsAttendanceDuration
 */
class AnalyticsAttendancesDurationTest extends KernelTestCase
{
    private ChildService $childService;

    public function setUp(): void
    {

        $name = 'dummy';
        $child = new Child($name);

        $id = 50;
        $prop = new ReflectionProperty(Child::class, 'id');
        $prop->setAccessible(true);
        $prop->setValue($child, $id);

        parent::setUp();
        $this->childService = $this->createMock(ChildService::class);
        $this->childService->expects($this->once())
            ->method('listChildrenByDuration')
            ->with(0, PHP_INT_MAX, 120)
            ->willReturn([$child]);
    }

    /**
     * @covers::__construct
     * @covers::execute
     * @covers::configure
     */
    public function testExecuteNoArg()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        self::getContainer()->set(ChildService::class, $this->childService);

        $command = $application->find('app:attendance:duration');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('dummy', $output);
    }

    /**
     * @covers::__construct
     * @covers::execute
     * @covers::configure
     */
    public function testExecuteWithArg()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        self::getContainer()->set(ChildService::class, $this->childService);

        $command = $application->find('app:attendance:duration');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['duration' => '120']);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('dummy', $output);
    }
}
