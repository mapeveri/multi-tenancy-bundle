<?php

namespace MultiTenancyBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use MultiTenancyBundle\Entity\Tenant;
use MultiTenancyBundle\Service\TenantCreateDatabase;
use MultiTenancyBundle\Service\TenantRemoveDatabase;
use MultiTenancyBundle\EventListener\EntityTenantEventListener;
use MultiTenancyBundle\Doctrine\Database\CreateDatabaseInterface;
use MultiTenancyBundle\Doctrine\Database\RemoveDatabaseInterface;

class EntityTenantEventListenerTest extends TestCase
{
    public function testPostPersist()
    {
        $createDatabaseMock = $this->createMock(CreateDatabaseInterface::class);
        $createDatabaseMock->expects($this->any())
            ->method('create')
            ->willReturn(null);
        $createDatabaseMock->expects($this->any())
            ->method('createUser')
            ->willReturn(null);
        
        $removeDatabaseMock = $this->createMock(RemoveDatabaseInterface::class);
        $removeDatabaseMock->expects($this->any())
            ->method('remove')
            ->willReturn(null);
        $removeDatabaseMock->expects($this->any())
            ->method('removeUser')
            ->willReturn(null);

        $tenantCreateDatabaseService = new TenantCreateDatabase($createDatabaseMock);
        $tenantRemoveDatabaseService = new TenantRemoveDatabase($removeDatabaseMock);

        $tenant = new Tenant();
        $tenant->setUuid("45b9d690-100c-4fa4-b133-996efdaf2499");

        $reflectionClass = new \ReflectionClass(get_class($tenant));
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($tenant, 1);

        $listener = new EntityTenantEventListener($tenantCreateDatabaseService, $tenantRemoveDatabaseService);
        $listener->postPersist($tenant);
        $listener->preRemove($tenant);

        $this->assertTrue(true);
    }
}
