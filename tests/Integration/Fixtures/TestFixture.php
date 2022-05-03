<?php

namespace App\Tests\Integration\Fixtures;

use App\Entity\Attendance;
use App\Entity\Child;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixture extends Fixture
{
    protected array $records;

    public function __construct()
    {
        $this->records = [];
    }

    public function getRecords(): array
    {
        return $this->records;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->records as $record) {
            $manager->persist($record);
        }

        $manager->flush();
    }

    public function addUser(
        UserPasswordHasherInterface $passwordHasher,
        $name = 'admin',
        string $password = 'admin',
        array $roles = ['ROLE_STAFF'],
        string $token = 'adminToken'
    ): TestFixture {
        $user = new User();

        $user->setUsername($name);
        $user->setRoles($roles);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $password
            )
        );
        $user->setApiToken($token);

        $this->records[] = $user;
        return $this;
    }

    public function addChild(string $name = 'dummy'): TestFixture
    {
        $this->records[] = new Child($name);
        return $this;
    }

    public function addAttendance(Child $child, ?DateTime $enterAt): TestFixture
    {
        $attendance = new Attendance($child);
        $attendance->setEnteredAt($enterAt);
        $this->records[] = $attendance;
        return $this;
    }
}
