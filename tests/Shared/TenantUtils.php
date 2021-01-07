<?php

namespace MultiTenancyBundle\Tests\Shared;

use MultiTenancyBundle\Entity\Tenant;
use MultiTenancyBundle\Entity\Hostname;

class TenantUtils
{
    public static function getTenant(): Hostname
    {
        $tenant = new Tenant();
        $tenant->setUuid('45b9d690-100c-4fa4-b133-996efdaf2499');

        $hostname = new Hostname();
        $hostname->setFqdn('tenant1');
        $hostname->setTenant($tenant);

        return $hostname;
    }
}
