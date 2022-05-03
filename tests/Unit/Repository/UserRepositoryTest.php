<?php

namespace App\Tests\Unit\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/** @coversDefaultClass \App\Repository\UserRepository */
class UserRepositoryTest extends TestCase
{
    protected EntityManagerInterface $entityManager;
    protected UserRepository $sut;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->sut = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept([
                'upgradePassword',
            ])
            ->getMock();

        $prop = new ReflectionProperty(UserRepository::class, '_em');
        $prop->setAccessible(true);
        $prop->setValue($this->sut, $this->entityManager);
    }

    /**
     * @covers::upgradePassword
     */
    public function testUpgradePasswordSuccess()
    {
        $model = new User();

        $this->entityManager->expects($this->once())->method('persist')->with($model);
        $this->entityManager->expects($this->once())->method('flush');

        $this->sut->upgradePassword($model, 'dummyPassword');
    }

    /**
     * @covers ::upgradePassword
     */
    public function testUpgradePasswordFailure()
    {
        $model = $this->createMock(PasswordAuthenticatedUserInterface::class);

        $this->expectException(UnsupportedUserException::class);

        $this->sut->upgradePassword($model, 'dummyPassword');
    }
}
