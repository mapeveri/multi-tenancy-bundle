<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database\MySql;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use MultiTenancyBundle\Doctrine\Database\RemoveDatabaseInterface;
use MultiTenancyBundle\Doctrine\Database\TenantConnectionTrait;

final class RemoveDatabaseMySql implements RemoveDatabaseInterface
{
    use TenantConnectionTrait;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        $this->em = $registry->getManager('default');
    }

    /**
     * Remove the database tenant
     *
     * @param string $dbName
     * @return void
     */
    public function remove(string $dbName): void
    {
        // Create the new database tenant
        $this->em->getConnection()->getSchemaManager()->dropDatabase("`$dbName`");
    }

    /**
     * Remove a user for the tenant database
     *
     * @param string $dbName
     * @return void
     */
    public function removeUser(string $dbName, int $tenantId): void
    {
        $params = $this->em->getConnection()->getParams();
        $conn = $this->getParamsConnectionTenant($dbName, $params);
        $user = $conn['user'] . "_{$tenantId}";

        $sql = <<<SQL
        DROP USER '{$user}';
        SQL;

        $this->em->getConnection()->executeStatement($sql);
    }
}
