<?php

namespace MultiTenancyBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use MultiTenancyBundle\Entity\Hostname;
use MultiTenancyBundle\Tests\Shared\TenantUtils;

class HostnameTest extends TestCase
{
    public function testEntity()
    {
        $utils = new TenantUtils();
        $tenant = $utils->getTenantObject();

        $dt = new \Datetime();
        $hostname = new Hostname();
        $hostname->setFqdn('tenant1');
        $hostname->setTenant($tenant);
        $hostname->setCreatedAt($dt);
        $hostname->setUpdatedAt($dt);
        $hostname->setDeletedAt($dt);

        $reflectionClass = new \ReflectionClass(get_class($hostname));
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($hostname, 1);

        $this->assertEquals("tenant1", $hostname->getFqdn());
        $this->assertEquals($dt, $hostname->getCreatedAt());
        $this->assertEquals($dt, $hostname->getUpdatedAt());
        $this->assertEquals($dt, $hostname->getDeletedAt());
        $this->assertEquals(1, $hostname->getId());
    }
}
