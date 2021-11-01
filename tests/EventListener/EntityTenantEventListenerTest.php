<?php

namespace MultiTenancyBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use MultiTenancyBundle\Entity\Tenant;
use MultiTenancyBundle\EventListener\EntityTenantEventListener;
use MultiTenancyBundle\Doctrine\Database\CreateTenantInterface;
use MultiTenancyBundle\Doctrine\Database\RemoveTenantInterface;

class EntityTenantEventListenerTest extends TestCase
{
    public function testPostPersist()
    {
        $createDatabaseMock = $this->createMock(CreateTenantInterface::class);
        $createDatabaseMock->expects($this->any())
            ->method('create')
            ->willReturn(null);

        $removeDatabaseMock = $this->createMock(RemoveTenantInterface::class);
        $removeDatabaseMock->expects($this->any())
            ->method('remove')
            ->willReturn(null);

        $tenant = new Tenant();
        $tenant->setUuid("45b9d690-100c-4fa4-b133-996efdaf2499");

        $reflectionClass = new \ReflectionClass(get_class($tenant));
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($tenant, 1);

        $listener = new EntityTenantEventListener($createDatabaseMock, $removeDatabaseMock);
        $listener->postPersist($tenant);
        $listener->preRemove($tenant);

        $this->assertTrue(true);
    }
}
