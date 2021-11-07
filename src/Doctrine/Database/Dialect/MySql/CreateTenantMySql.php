<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database\Dialect\MySql;

use Doctrine\Persistence\ManagerRegistry;
use MultiTenancyBundle\Doctrine\Database\CreateSchemaFactory;
use MultiTenancyBundle\Doctrine\Database\CreateTenantInterface;
use MultiTenancyBundle\Doctrine\Database\EntityManagerFactory;
use MultiTenancyBundle\Doctrine\Database\TenantConnectionTrait;
use MultiTenancyBundle\Event\CreateTenantEvent;
use MultiTenancyBundle\Event\MultiTenancyEvents;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CreateTenantMySql implements CreateTenantInterface
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
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerFactory $emFactory,
        CreateSchemaFactory $createSchemaFactory,
        EventDispatcherInterface $dispatcher
    ) {
        $this->emTenant = $registry->getManager('tenant');
        $this->emFactory = $emFactory;
        $this->createSchemaFactory = $createSchemaFactory;
        $this->dispatcher = $dispatcher;
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
        $this->emTenant->getConnection()->getSchemaManager()->createDatabase("`$dbName`");

        // Set the database
        $conn = $this->getParamsConnectionTenant($dbName, $params);

        // Get the metadata
        $newEmTenant = $this->emFactory->create($conn, $this->emTenant->getConfiguration(), $this->emTenant->getEventManager());
        $meta = $newEmTenant->getMetadataFactory()->getAllMetadata();

        // Create tables schemas
        $this->createSchemaFactory->create($newEmTenant, $meta);

        $this->createUser($dbName, $tenantId);

        $event = new CreateTenantEvent($dbName, $tenantId);
        $this->dispatcher->dispatch($event, MultiTenancyEvents::TENANT_CREATED);
    }

    /**
     * Create a new user for the new tenant database
     *
     * @param string $dbName
     * @param int $tenantId
     * @return void
     */
    private function createUser(string $dbName, int $tenantId): void
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
