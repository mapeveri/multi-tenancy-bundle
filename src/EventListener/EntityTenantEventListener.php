<?php

declare(strict_types=1);

namespace MultiTenancyBundle\EventListener;

use MultiTenancyBundle\Entity\Tenant;
use MultiTenancyBundle\Service\TenantCreateDatabase;

final class EntityTenantEventListener
{
    /**
     * @var tenantCreateDatabase
     */
    private $tenantCreateDatabase;

    public function __construct(TenantCreateDatabase $tenantCreateDatabase)
    {
        $this->tenantCreateDatabase = $tenantCreateDatabase;
    }

    /**
     * After persist a new tenant, this create the schema on the database
     *
     * @param Tenant $args
     * @return void
     */
    public function postPersist(Tenant $args): void
    {
        $this->tenantCreateDatabase->create($args->getUuid(), $args->getId());
    }
}
