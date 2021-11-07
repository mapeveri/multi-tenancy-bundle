<?php

namespace MultiTenancyBundle\Tests\Doctrine\Database\Dialect\MySql;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use MultiTenancyBundle\Event\RemoveTenantEvent;
use MultiTenancyBundle\Event\MultiTenancyEvents;
use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use MultiTenancyBundle\Doctrine\Database\Dialect\MySql\RemoveTenantMySql;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RemoveDatabaseMySqlTest extends TestCase
{
    private $managerRegistry;
    private $dispatcher;

    /**
     * @var array
     */
    protected $params = [
        'driver' => 'pdo_mysql',
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
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        
        $connection->expects($this->any())
            ->method('getSchemaManager')
            ->willReturn($schemaManager);

        $connection->expects($this->any())
            ->method('getParams')
            ->willReturn($this->params);

        $schemaManager->expects($this->any())
            ->method('dropDatabase')
            ->willReturn("");
        
        $entityManager->expects($this->any())
            ->method('getConnection')
            ->willReturn($connection);

        // Construct parameters
        $this->managerRegistry->expects($this->any())
            ->method('getManager')
            ->willReturn($entityManager);
    }

    public function testRemoveDatabase()
    {
        $removeDatabaseMySql = new RemoveTenantMySql($this->managerRegistry, $this->dispatcher);

        $event = new RemoveTenantEvent('testing', 1);
        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->equalTo($event),
                $this->equalTo(MultiTenancyEvents::TENANT_REMOVED)
            );

        $removeDatabaseMySql->remove('testing',1);
        $this->assertTrue(true);
    }
}
