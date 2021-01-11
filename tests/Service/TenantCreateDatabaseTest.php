<?php

namespace MultiTenancyBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use MultiTenancyBundle\Service\TenantCreateDatabase;
use MultiTenancyBundle\Doctrine\Database\CreateDatabaseInterface;

class TenantCreateDatabaseTest extends TestCase
{
    public function testCreateDatabase()
    {
        $createDatabaseMock = $this->createMock(CreateDatabaseInterface::class);
        $createDatabaseMock->expects($this->any())
            ->method('createDatabase')
            ->willReturn(null);
        $createDatabaseMock->expects($this->any())
            ->method('createDatabaseUser')
            ->willReturn(null);

        $tenantDatabaseService = new TenantCreateDatabase($createDatabaseMock);

        $this->assertEquals(null, $tenantDatabaseService->create("databaseName", 1));
    }
}
