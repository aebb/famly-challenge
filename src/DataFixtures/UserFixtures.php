<?php

namespace App\DataFixtures;

use App\Entity\Child;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @codeCoverageIgnore
 */
class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {

        $staff1 = new User();
        $staff1->setUsername('staff1');
        $staff1->setRoles([USER::ROLES['ROLE_STAFF']]);
        $staff1->setApiToken('staff1token');
        $staff1->setPassword(
            $this->passwordHasher->hashPassword(
                $staff1,
                'staff1'
            )
        );

        $kid1 = new Child('Liam');
        $kid2 = new Child('Noah');
        $kid3 = new Child('Oliver');
        $kid4 = new Child('Henry');
        $kid5 = new Child('John');
        $kid6 = new Child('Olivia');
        $kid7 = new Child('Emma');
        $kid8 = new Child('Charlotte');

        $manager->persist($staff1);
        $manager->persist($kid1);
        $manager->persist($kid2);
        $manager->persist($kid3);
        $manager->persist($kid4);
        $manager->persist($kid5);
        $manager->persist($kid6);
        $manager->persist($kid7);
        $manager->persist($kid8);

        $manager->flush();
    }
}
