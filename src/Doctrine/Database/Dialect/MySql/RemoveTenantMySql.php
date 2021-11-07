<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database\Dialect\MySql;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use MultiTenancyBundle\Doctrine\Database\RemoveTenantInterface;
use MultiTenancyBundle\Event\MultiTenancyEvents;
use MultiTenancyBundle\Event\RemoveTenantEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RemoveTenantMySql implements RemoveTenantInterface
{
    /**
     * @var EntityManager
     */
    protected $emTenant;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(ManagerRegistry $registry, EventDispatcherInterface $dispatcher)
    {
        $this->emTenant = $registry->getManager('tenant');
        $this->dispatcher = $dispatcher;
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

        $event = new RemoveTenantEvent($dbName, $tenantId);
        $this->dispatcher->dispatch($event, MultiTenancyEvents::TENANT_REMOVED);
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
