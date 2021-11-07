<?php

namespace MultiTenancyBundle\Tests\Doctrine\DBAL;

use MultiTenancyBundle\Doctrine\Database\Dialect\Driver;
use MultiTenancyBundle\Doctrine\Database\Dialect\MySql\RemoveTenantMySql;
use MultiTenancyBundle\Doctrine\Database\Dialect\PostgreSql\RemoveTenantPsql;
use MultiTenancyBundle\Doctrine\Database\Dialect\RemoveTenantFactory;
use MultiTenancyBundle\Doctrine\DBAL\TenantConnectionInterface;
use PHPUnit\Framework\TestCase;

class RemoveTenantFactoryTest extends TestCase
{
    public function testFactoryMySql()
    {
        $tenantConnection = $this->createMock(TenantConnectionInterface::class);
        $tenantConnection->expects($this->once())
            ->method('getDriverConnection')
            ->willReturn(Driver::MYSQL);

        $factory = new RemoveTenantFactory();
        $factory->setRemoveTenantMySql($this->createMock(RemoveTenantMySql::class));
        $conn = $factory($tenantConnection);

        $this->assertTrue($conn instanceof RemoveTenantMySql);
    }

    public function testFactoryPostgreSql()
    {
        $tenantConnection = $this->createMock(TenantConnectionInterface::class);
        $tenantConnection->expects($this->once())
            ->method('getDriverConnection')
            ->willReturn(Driver::POSTGRESQL);

        $factory = new RemoveTenantFactory();
        $factory->setRemoveTenantPsql($this->createMock(RemoveTenantPsql::class));
        $conn = $factory($tenantConnection);

        $this->assertTrue($conn instanceof RemoveTenantPsql);
    }
}