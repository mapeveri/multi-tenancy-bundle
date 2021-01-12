<?php

namespace MultiTenancyBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use MultiTenancyBundle\Entity\Tenant;
use MultiTenancyBundle\Service\TenantCreateDatabase;
use MultiTenancyBundle\EventListener\EntityTenantEventListener;
use MultiTenancyBundle\Doctrine\Database\CreateDatabaseInterface;

class EntityTenantEventListenerTest extends TestCase
{
    public function testPostPersist()
    {
        $createDatabaseMock = $this->createMock(CreateDatabaseInterface::class);
        $createDatabaseMock->expects($this->any())
            ->method('createDatabase')
            ->willReturn(null);
        $createDatabaseMock->expects($this->any())
            ->method('createDatabaseUser')
            ->willReturn(null);

        $tenantDatabaseService = new TenantCreateDatabase($createDatabaseMock);

        $tenant = new Tenant();
        $tenant->setUuid("45b9d690-100c-4fa4-b133-996efdaf2499");

        $reflectionClass = new \ReflectionClass(get_class($tenant));
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($tenant, 1);

        $listener = new EntityTenantEventListener($tenantDatabaseService);
        $listener->postPersist($tenant);

        $this->assertTrue(true);
    }
}
