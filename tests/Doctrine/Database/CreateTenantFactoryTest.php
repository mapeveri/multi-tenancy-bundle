<?php

namespace MultiTenancyBundle\Tests\Doctrine\DBAL;

use MultiTenancyBundle\Doctrine\Database\Dialect\CreateTenantFactory;
use MultiTenancyBundle\Doctrine\Database\Dialect\Driver;
use MultiTenancyBundle\Doctrine\Database\Dialect\MySql\CreateTenantMySql;
use MultiTenancyBundle\Doctrine\Database\Dialect\PostgreSql\CreateTenantPsql;
use MultiTenancyBundle\Doctrine\DBAL\TenantConnectionInterface;
use PHPUnit\Framework\TestCase;

class CreateTenantFactoryTest extends TestCase
{
    public function testFactoryMySql()
    {
        $tenantConnection = $this->createMock(TenantConnectionInterface::class);
        $tenantConnection->expects($this->once())
            ->method('getDriverConnection')
            ->willReturn(Driver::MYSQL);

        $factory = new CreateTenantFactory();
        $factory->setCreateTenantMySql($this->createMock(CreateTenantMySql::class));
        $conn = $factory($tenantConnection);

        $this->assertTrue($conn instanceof CreateTenantMySql);
    }

    public function testFactoryPostgreSql()
    {
        $tenantConnection = $this->createMock(TenantConnectionInterface::class);
        $tenantConnection->expects($this->once())
            ->method('getDriverConnection')
            ->willReturn(Driver::POSTGRESQL);

        $factory = new CreateTenantFactory();
        $factory->setCreateTenantPsql($this->createMock(CreateTenantPsql::class));
        $conn = $factory($tenantConnection);

        $this->assertTrue($conn instanceof CreateTenantPsql);
    }
}
