<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database\Dialect\PostgreSql;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use MultiTenancyBundle\Doctrine\Database\RemoveTenantInterface;

class RemoveTenantPsql implements RemoveTenantInterface
{
    /**
     * @var EntityManager
     */
    protected $emTenant;

    public function __construct(ManagerRegistry $registry)
    {
        $this->emTenant = $registry->getManager('tenant');
    }

    /**
     * Remove the database tenant
     *
     * @param string $dbName
     * @param int $tenantId
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function remove(string $dbName, int $tenantId): void
    {
        // Remove the schema
        $this->emTenant->getConnection()->executeStatement("DROP SCHEMA \"{$dbName}\" CASCADE");
    }
}