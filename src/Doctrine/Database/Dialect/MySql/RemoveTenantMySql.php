<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database\Dialect\MySql;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use MultiTenancyBundle\Doctrine\Database\RemoveTenantInterface;

class RemoveTenantMySql implements RemoveTenantInterface
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
     */
    public function remove(string $dbName, int $tenantId): void
    {
        // Remove the new database tenant
        $this->emTenant->getConnection()->getSchemaManager()->dropDatabase("`$dbName`");
        $this->removeUser($dbName, $tenantId);
    }

    /**
     * Remove a user for the tenant database
     *
     * @param string $dbName
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    private function removeUser(string $dbName, int $tenantId): void
    {
        $conn = $this->emTenant->getConnection()->getParams();
        $user = $conn['user'] . "_{$tenantId}";

        $sql = <<<SQL
        DROP USER '{$user}';
        SQL;

        $this->emTenant->getConnection()->executeStatement($sql);
    }
}
