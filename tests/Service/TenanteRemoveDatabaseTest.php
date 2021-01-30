<?php

namespace MultiTenancyBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use MultiTenancyBundle\Service\TenantRemoveDatabase;
use MultiTenancyBundle\Doctrine\Database\RemoveDatabaseInterface;

class TenantRemoveDatabaseTest extends TestCase
{
    public function testRemoveDatabase()
    {
        $removeDatabaseMock = $this->createMock(RemoveDatabaseInterface::class);
        $removeDatabaseMock->expects($this->any())
            ->method('remove')
            ->willReturn(null);
        $removeDatabaseMock->expects($this->any())
            ->method('removeUser')
            ->willReturn(null);

        $tenantDatabaseService = new TenantRemoveDatabase($removeDatabaseMock);

        $this->assertEquals(null, $tenantDatabaseService->remove("databaseName", 1));
    }
}
