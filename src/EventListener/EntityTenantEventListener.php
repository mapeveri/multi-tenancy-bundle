<?php

declare(strict_types=1);

namespace MultiTenancyBundle\EventListener;

use MultiTenancyBundle\Entity\Tenant;
use MultiTenancyBundle\Service\TenantCreateDatabase;
use MultiTenancyBundle\Service\TenantRemoveDatabase;

final class EntityTenantEventListener
{
    /**
     * @var tenantCreateDatabase
     */
    private $tenantCreateDatabase;

    /**
     * @var TenantRemoveDatabase
     */
    private $tenantRemoveDatabase;

    public function __construct(TenantCreateDatabase $tenantCreateDatabase, TenantRemoveDatabase $tenantRemoveDatabase)
    {
        $this->tenantCreateDatabase = $tenantCreateDatabase;
        $this->tenantRemoveDatabase = $tenantRemoveDatabase;
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

    /**
     * Pre remove a tenant, this remove the schema on the database
     *
     * @param Tenant $args
     * @return void
     */
    public function preRemove(Tenant $args)
    {
        $this->tenantRemoveDatabase->remove($args->getUuid(), $args->getId());
    }
}
