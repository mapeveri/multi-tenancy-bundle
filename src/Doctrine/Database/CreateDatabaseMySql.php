<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use MultiTenancyBundle\Doctrine\Database\CreateSchemaFactory;
use MultiTenancyBundle\Doctrine\Database\EntityManagerFactory;
use MultiTenancyBundle\Doctrine\Database\CreateDatabaseInterface;

final class CreateDatabaseMySql implements CreateDatabaseInterface
{
    /**
     * @var EntityManager
     */
    private $em;
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
        $this->em = $registry->getManager('default');
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
    public function createDatabase(string $dbName): void
    {
        // Create the new database tenant
        $this->em->getConnection()->getSchemaManager()->createDatabase("`$dbName`");

        // Set a new connection to the new tenant
        $conn = $this->getParamsConnectionTenant($dbName);
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
    public function createDatabaseUser(string $dbName, int $tenantId): void
    {
        $conn = $this->getParamsConnectionTenant($dbName);
        $user = $conn['user'] . "_{$tenantId}";
        $password = $conn['password'];
        $host = $conn['host'];

        $sql = <<<SQL
        CREATE USER '{$user}'@'%' IDENTIFIED BY '{$password}';
        GRANT ALL ON `{$dbName}`.* TO '{$user}'@'{$host}';
        SQL;

        $this->em->getConnection()->executeStatement($sql);
    }


    /**
     * Get tenant connection parameters
     *
     * @param string $dbName
     * @return array
     */
    private function getParamsConnectionTenant(string $dbName): array
    {
        $params = $this->em->getConnection()->getParams();
        $conn = array(
            'driver' => $params['driver'],
            'host' => $params['host'],
            'user' => $params['user'],
            'password' => $params['password'],
            'dbname' => $dbName
        );

        return $conn;
    }
}
