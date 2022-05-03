<?php

namespace App\Tests\Integration;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class EndpointTester extends WebTestCase
{
    public const MANAGER_ID_DEFAULT = 'doctrine.orm.default_entity_manager';

    public const MESSENGER_TRANSPORT = 'messenger.transport.async';

    public const PASSWORD_ENCODER = 'security.password_hasher';

    protected KernelBrowser $client;
    protected EntityManagerInterface $defaultManager;
    protected UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->defaultManager = self::getContainer()->get(self::MANAGER_ID_DEFAULT);

        $this->passwordHasher = self::getContainer()->get(self::PASSWORD_ENCODER);

        $schemaTool = new SchemaTool($this->defaultManager);
        $schemaTool->updateSchema($this->defaultManager->getMetadataFactory()->getAllMetadata());
    }

    public function loadFixture(Fixture $fixture)
    {
        $loader = new Loader();
        $fixtures = is_array($fixture) ? $fixture : [$fixture];
        foreach ($fixtures as $item) {
            $loader->addFixture($item);
        }

        $manager = self::$kernel->getContainer()->get(self::MANAGER_ID_DEFAULT);
        $executor = new ORMExecutor($manager, new ORMPurger());
        $executor->execute($loader->getFixtures());
        $manager->flush();
    }
}
