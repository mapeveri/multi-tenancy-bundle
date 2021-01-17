<?php

namespace MultiTenancyBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use MultiTenancyBundle\Exception\TenantNotFound;
use MultiTenancyBundle\Tests\Shared\TenantUtils;
use MultiTenancyBundle\Service\TenantDatabaseName;
use MultiTenancyBundle\Repository\HostnameRepository;

class TenantDatabaseNameTest extends TestCase
{
    public function testGetName()
    {
        $utils = new TenantUtils();
        $hostname = $utils->getTenant();

        $hostnameRepository = $this->createMock(HostnameRepository::class);
        $hostnameRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($hostname);

        $tenantDatabaseName = new TenantDatabaseName($hostnameRepository);

        $this->assertEquals("45b9d690-100c-4fa4-b133-996efdaf2499", $tenantDatabaseName->getName());
        $this->assertEquals("45b9d690-100c-4fa4-b133-996efdaf2499", $tenantDatabaseName->getName("tenant1"));
        $this->assertNotEquals("45b9d690", $tenantDatabaseName->getName("tenant1"));
    }

    public function testGetNameException()
    {
        $hostnameRepository = $this->createMock(HostnameRepository::class);
        $hostnameRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);

        $this->expectException(TenantNotFound::class);

        $tenantDatabaseName = new TenantDatabaseName($hostnameRepository);
        $tenantDatabaseName->getName();
    }
}
