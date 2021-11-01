<?php

namespace MultiTenancyBundle\Tests\Doctrine\Database\Dialect\PostgreSql;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use MultiTenancyBundle\Doctrine\Database\Dialect\PostgreSql\RemoveTenantPsql;
use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ManagerRegistry;

class RemoveDatabasePsqlTest extends TestCase
{
    private $managerRegistry;

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
        $removeDatabasePsql = new RemoveTenantPsql($this->managerRegistry);
        $removeDatabasePsql->remove('testing',1);
        $this->assertTrue(true);
    }
}
