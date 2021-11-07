<?php

namespace MultiTenancyBundle\Tests\Doctrine\Database\Dialect\PostgreSql;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use MultiTenancyBundle\Doctrine\Database\Dialect\PostgreSql\RemoveTenantPsql;
use MultiTenancyBundle\Event\RemoveTenantEvent;
use MultiTenancyBundle\Event\MultiTenancyEvents;
use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RemoveDatabasePsqlTest extends TestCase
{
    private $managerRegistry;
    private $dispatcher;

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
        $connection = $this->createMock(Connection::class);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);

        $entityManager->expects($this->once())
            ->method('getConnection')
            ->willReturn($connection);

        $connection->expects($this->once())
            ->method('executeStatement');

        $this->managerRegistry->expects($this->any())
            ->method('getManager')
            ->willReturn($entityManager);
    }

    public function testRemoveDatabase()
    {
        $removeDatabasePsql = new RemoveTenantPsql($this->managerRegistry, $this->dispatcher);

        $event = new RemoveTenantEvent('testing', 1);
        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->equalTo($event),
                $this->equalTo(MultiTenancyEvents::TENANT_REMOVED)
            );

        $removeDatabasePsql->remove('testing',1);
        $this->assertTrue(true);
    }
}
