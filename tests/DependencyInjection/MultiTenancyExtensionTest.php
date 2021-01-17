<?php

namespace MultiTenancyBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use MultiTenancyBundle\DependencyInjection\MultiTenancyExtension;
use MultiTenancyBundle\Doctrine\Database\CreateDatabaseInterface;

class MultiTenancyExtensionTest extends TestCase
{
    public function testLoadExtension(): void
    {
        if (! interface_exists(EntityManagerInterface::class)) {
            self::markTestSkipped('This test requires ORM');
        }

        $container = new ContainerBuilder();
        $extension = new MultiTenancyExtension();
        $extension->load([], $container);

        // Total services defined
        $this->assertTrue(count($container->getDefinitions()) === 15);
    }
}
