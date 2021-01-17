<?php

namespace MultiTenancyBundle\Tests\Command;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use MultiTenancyBundle\Command\Migration\DependencyFactory;

class DependencyFactoryTest extends TestCase
{
    public function testCreate()
    {
        $em = $this->createMock(EntityManager::class);

        $tenantConnectionWrapper = new DependencyFactory();
        $tenantConnectionWrapper->create($em, "tenant", dirname(__DIR__));

        $this->assertTrue(true);
    }
}
