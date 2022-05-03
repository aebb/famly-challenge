<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/** @coversDefaultClass \App\Entity\User */
class UserTest extends TestCase
{
    /**
     * @covers ::getId
     * @covers ::getRoles
     * @covers ::setRoles
     * @covers ::getPassword
     * @covers ::setPassword
     * @covers ::getSalt
     * @covers ::getUserIdentifier
     * @covers ::setUsername
     * @covers ::getApiToken
     * @covers ::setApiToken
     * @covers ::eraseCredentials
     * @covers ::isStaff
     */
    public function testUser()
    {
        $id = 1;
        $password = 'dummy_password';
        $roles = ['ROLE_STAFF'];
        $username = 'dummy_username';
        $apiToken = 'apiToken';

        $model = new User();

        $model->setPassword($password);
        $model->setRoles($roles);
        $model->setUsername($username);
        $model->setApiToken($apiToken);

        $propId = new ReflectionProperty(User::class, 'id');
        $propId->setAccessible(true);
        $propId->setValue($model, $id);

        $this->assertEquals($id, $model->getId());
        $this->assertNull($model->getSalt());
        $this->assertEquals($username, $model->getUserIdentifier());
        $this->assertEquals($password, $model->getPassword());
        $this->assertEquals($roles, $model->getRoles());
        $this->assertEquals($apiToken, $model->getApiToken());
        $this->assertTrue($model->isStaff());
        $this->assertNull($model->eraseCredentials());
    }
}
