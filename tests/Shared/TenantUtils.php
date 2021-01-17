<?php

namespace MultiTenancyBundle\Tests\Shared;

use MultiTenancyBundle\Entity\Tenant;
use MultiTenancyBundle\Entity\Hostname;

class TenantUtils
{
    public function getTenantObject(): Tenant
    {
        $tenant = new Tenant();
        $tenant->setUuid('45b9d690-100c-4fa4-b133-996efdaf2499');

        return $tenant;
    }

    public function getArrayTenants(): array
    {
        $tenants = [];
        $tenants[] = $this->getTenantObject();
        return $tenants;
    }

    public function getTenant(): Hostname
    {
        $tenant = $this->getTenantObject();

        $hostname = new Hostname();
        $hostname->setFqdn('tenant1');
        $hostname->setTenant($tenant);

        return $hostname;
    }
}
