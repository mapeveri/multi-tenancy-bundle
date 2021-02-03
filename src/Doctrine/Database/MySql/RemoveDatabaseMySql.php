<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database\MySql;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use MultiTenancyBundle\Doctrine\Database\RemoveDatabaseInterface;

final class RemoveDatabaseMySql implements RemoveDatabaseInterface
{
    /**
     * @var EntityManager
     */
    private $emTenant;

    public function __construct(ManagerRegistry $registry)
    {
        $this->emTenant = $registry->getManager('tenant');
    }

    /**
     * Remove the database tenant
     *
     * @param string $dbName
     * @return void
     */
    public function remove(string $dbName): void
    {
        // Remove the new database tenant
        $params = $this->emTenant->getConnection()->getParams();
        $this->emTenant->getConnection()->getSchemaManager()->dropDatabase("`$dbName`");
    }

    /**
     * Remove a user for the tenant database
     *
     * @param string $dbName
     * @return void
     */
    public function removeUser(string $dbName, int $tenantId): void
    {
        $conn = $this->emTenant->getConnection()->getParams();
        $user = $conn['user'] . "_{$tenantId}";

        $sql = <<<SQL
        DROP USER '{$user}';
        SQL;

        $this->emTenant->getConnection()->executeStatement($sql);
    }
}
