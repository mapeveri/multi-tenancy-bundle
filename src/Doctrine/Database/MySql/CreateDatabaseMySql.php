<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database\MySql;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use MultiTenancyBundle\Doctrine\Database\CreateSchemaFactory;
use MultiTenancyBundle\Doctrine\Database\EntityManagerFactory;
use MultiTenancyBundle\Doctrine\Database\CreateDatabaseInterface;
use MultiTenancyBundle\Doctrine\Database\TenantConnectionTrait;

final class CreateDatabaseMySql implements CreateDatabaseInterface
{
    use TenantConnectionTrait;

    /**
     * @var EntityManager
     */
    private $emTenant;
    /**
     * @var EntityManagerFactory
     */
    private $emFactory;
    /**
     * @var CreateSchemaFactory
     */
    private $createSchemaFactory;

    public function __construct(ManagerRegistry $registry, EntityManagerFactory $emFactory, CreateSchemaFactory $createSchemaFactory)
    {
        $this->emTenant = $registry->getManager('tenant');
        $this->emFactory = $emFactory;
        $this->createSchemaFactory = $createSchemaFactory;
    }

    /**
     * Create the database tenant
     *
     * @param string $dbName
     * @return void
     */
    public function create(string $dbName): void
    {
        // Set a new connection to the new tenant
        $params = $this->emTenant->getConnection()->getParams();
        
        // Create the new database tenant
        $this->emTenant->getConnection()->getSchemaManager()->createDatabase("`$dbName`");

        // Set the database
        $conn = $this->getParamsConnectionTenant($dbName, $params);

        // Get the metadata
        $newEmTenant = $this->emFactory->create($conn, $this->emTenant->getConfiguration(), $this->emTenant->getEventManager());
        $meta = $newEmTenant->getMetadataFactory()->getAllMetadata();

        // Create tables schemas
        $this->createSchemaFactory->create($newEmTenant, $meta);
    }

    /**
     * Create a new user for the new tenant database
     *
     * @param string $dbName
     * @return void
     */
    public function createUser(string $dbName, int $tenantId): void
    {
        $params = $this->emTenant->getConnection()->getParams();
        $conn = $this->getParamsConnectionTenant($dbName, $params);

        $user = $conn['user'] . "_{$tenantId}";
        $password = $conn['password'];
        $host = $conn['host'];

        $sql = <<<SQL
        CREATE USER '{$user}'@'%' IDENTIFIED BY '{$password}';
        GRANT ALL ON `{$dbName}`.* TO '{$user}'@'{$host}';
        SQL;

        $this->emTenant->getConnection()->executeStatement($sql);
    }
}
