<?php

namespace MultiTenancyBundle\Tests\Doctrine\DBAL;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Configuration;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\EventManager;
use MultiTenancyBundle\Doctrine\Database\EntityManagerFactory;

class EntityManagerFactoryTest extends TestCase
{
    public function testCreate()
    {
        $evManager = $this->createMock(EventManager::class);

        $paths = array('/path/to/entity/mapping/files');
        $config = Setup::createAnnotationMetadataConfiguration($paths);
        $dbParams = array('driver' => 'pdo_sqlite', 'memory' => true);

        $tenantConnectionWrapper = new EntityManagerFactory();
        $tenantConnectionWrapper->create($dbParams, $config, $evManager);

        $this->assertTrue(true);
    }
}
