<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database\Dialect\PostgreSql;

use Doctrine\Persistence\ManagerRegistry;
use MultiTenancyBundle\Doctrine\Database\CreateSchemaFactory;
use MultiTenancyBundle\Doctrine\Database\CreateTenantInterface;
use MultiTenancyBundle\Doctrine\Database\EntityManagerFactory;
use MultiTenancyBundle\Doctrine\Database\TenantConnectionTrait;

class CreateTenantPsql implements CreateTenantInterface
{
    use TenantConnectionTrait;

    /**
     * @var EntityManagerFactory
     */
    protected $emTenant;
    /**
     * @var EntityManagerFactory
     */
    private $emFactory;
    /**
     * @var CreateSchemaFactory
     */
    private $createSchemaFactory;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerFactory $emFactory,
        CreateSchemaFactory $createSchemaFactory
    ) {
        $this->emTenant = $registry->getManager('tenant');
        $this->emFactory = $emFactory;
        $this->createSchemaFactory = $createSchemaFactory;
    }

    /**
     * Create the database tenant
     *
     * @param string $dbName
     * @param int $tenantId
     * @return void
     */
    public function create(string $dbName, int $tenantId): void
    {
        // Set a new connection to the new tenant
        $params = $this->emTenant->getConnection()->getParams();

        // Create the new database tenant
        PsqlUtils::createSchema($this->emTenant->getConnection(), $dbName);

        // Set the database
        $conn = $this->getParamsConnectionTenant($params['dbname'], $params);

        // Get the metadata
        $newEmTenant = $this->emFactory->create($conn, $this->emTenant->getConfiguration(), $this->emTenant->getEventManager());

        PsqlUtils::setSchema($newEmTenant->getConnection(), $dbName);

        $meta = $newEmTenant->getMetadataFactory()->getAllMetadata();

        // Create tables schemas
        $this->createSchemaFactory->create($newEmTenant, $meta);
    }
}