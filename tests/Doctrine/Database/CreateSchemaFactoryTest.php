<?php

namespace MultiTenancyBundle\Tests\Doctrine\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Schema\SchemaConfig;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use MultiTenancyBundle\Doctrine\Database\CreateSchemaFactory;

class CreateSchemaFactoryTest extends TestCase
{
    public function testCreate()
    {
        $em = $this->createMock(EntityManager::class);
        $connection = $this->createMock(Connection::class);
        $config = $this->createMock(Configuration::class);
        $schemaManager = $this->createMock(AbstractSchemaManager::class);
        $schemaConfig = $this->createMock(SchemaConfig::class);
        $abstractPlatform = $this->createMock(AbstractPlatform::class);
        $evManager = $this->createMock(EventManager::class);

        $schemaManager->expects($this->any())
            ->method('createSchemaConfig')
            ->willReturn($schemaConfig);

        $connection->expects($this->any())
            ->method('getDatabasePlatform')
            ->willReturn($abstractPlatform);

        $connection->expects($this->any())
            ->method('getSchemaManager')
            ->willReturn($schemaManager);

        $em->expects($this->any())
            ->method('getEventManager')
            ->willReturn($evManager);

        $em->expects($this->any())
            ->method('getConnection')
            ->willReturn($connection);

        $em->expects($this->any())
            ->method('getConfiguration')
            ->willReturn($config);

        $createSchema = new CreateSchemaFactory();
        $createSchema->create($em, []);

        $this->assertTrue(true);
    }
}
