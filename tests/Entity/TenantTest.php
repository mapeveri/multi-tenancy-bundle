<?php

namespace MultiTenancyBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use MultiTenancyBundle\Entity\Hostname;
use Doctrine\Common\Collections\ArrayCollection;
use MultiTenancyBundle\Tests\Shared\TenantUtils;

class TenantTest extends TestCase
{
    public function testEntity()
    {
        $utils = new TenantUtils();
        $tenant = $utils->getTenantObject();

        $dt = new \Datetime();
        $tenant->setCreatedAt($dt);
        $tenant->setUpdatedAt($dt);
        $tenant->setDeletedAt($dt);

        $reflectionClass = new \ReflectionClass(get_class($tenant));
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($tenant, 1);

        $hostname = new Hostname();
        $hostname->setFqdn('tenant1');
        $tenant->addHostname($hostname);

        $uuid = "45b9d690-100c-4fa4-b133-996efdaf2499";
        $this->assertEquals($uuid, $tenant->getUuid());
        $this->assertEquals($dt, $tenant->getCreatedAt());
        $this->assertEquals($dt, $tenant->getUpdatedAt());
        $this->assertEquals($dt, $tenant->getDeletedAt());
        $this->assertEquals(1, $tenant->getId());
        $this->assertEquals(1, count($tenant->getHostnames()));

        $data = [
            'id' => 1,
            'uuid' => $uuid,
            'created_at' => $dt,
            'hostnames' => new ArrayCollection(['tenant1'])
        ];

        $this->assertEquals($data, $tenant->jsonSerialize());

        $tenant->removeHostname($hostname);
        $this->assertEquals(0, count($tenant->getHostnames()));
    }
}
