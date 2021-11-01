<?php

namespace MultiTenancyBundle\Tests\Doctrine\Database\Dialect\PostgreSql;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use MultiTenancyBundle\Doctrine\Database\Dialect\PostgreSql\CreateTenantPsql;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\EventManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use MultiTenancyBundle\Doctrine\Database\CreateSchemaFactory;
use MultiTenancyBundle\Doctrine\Database\EntityManagerFactory;

class CreateDatabasePsqlTest extends TestCase
{
    private $managerRegistry;
    private $emFactory;
    private $createSchemaFactory;

    /**
     * @var array
     */
    protected $params = [
        'driver' => 'pdo_psql',
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'password',
        'port' => '1234',
        'dbname' => 'main'
    ];

    public function setUp()
    {
        $entityManager = $this->createMock(EntityManager::class);
        $schemaManager = $this->createMock(AbstractSchemaManager::class);
        $connection = $this->createMock(Connection::class);
        $configuration = $this->createMock(Configuration::class);
        $eventManager = $this->createMock(EventManager::class);
        $classMetadataFactory = $this->createMock(ClassMetadataFactory::class);
        $this->createSchemaFactory = $this->createMock(CreateSchemaFactory::class);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->emFactory = $this->createMock(EntityManagerFactory::class);

        $connection->expects($this->any())
            ->method('getSchemaManager')
            ->willReturn($schemaManager);

        $connection->expects($this->any())
            ->method('getParams')
            ->willReturn($this->params);

        $schemaManager->expects($this->never())
            ->method('createDatabase')
            ->willReturn("");

        $classMetadataFactory->expects($this->any())
            ->method('getAllMetadata')
            ->willReturn([]);

        // Entity manager mock
        $entityManager->expects($this->any())
            ->method('getEventManager')
            ->willReturn($eventManager);

        $entityManager->expects($this->any())
            ->method('getMetadataFactory')
            ->willReturn($classMetadataFactory);

        $entityManager->expects($this->any())
            ->method('getConnection')
            ->willReturn($connection);

        $entityManager->expects($this->any())
            ->method('getConfiguration')
            ->willReturn($configuration);

        // Construct parameters
        $this->managerRegistry->expects($this->any())
            ->method('getManager')
            ->willReturn($entityManager);

        $this->emFactory->expects($this->any())
            ->method('create')
            ->willReturn($entityManager);
    }

    public function testCreateDatabase()
    {
        $createDatabasePsql = new CreateTenantPsql($this->managerRegistry, $this->emFactory, $this->createSchemaFactory);
        $createDatabasePsql->create('testing', 1);
        $this->assertTrue(true);
    }
}
