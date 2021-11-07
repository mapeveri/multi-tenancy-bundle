<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database\Dialect\PostgreSql;

use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use MultiTenancyBundle\Doctrine\Database\RemoveTenantInterface;
use MultiTenancyBundle\Event\MultiTenancyEvents;
use MultiTenancyBundle\Event\RemoveTenantEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RemoveTenantPsql implements RemoveTenantInterface
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
     * @throws \Doctrine\DBAL\Exception
     */
    public function remove(string $dbName, int $tenantId): void
    {
        // Remove the schema
        $this->emTenant->getConnection()->executeStatement("DROP SCHEMA \"{$dbName}\" CASCADE");

        $event = new RemoveTenantEvent($dbName, $tenantId);
        $this->dispatcher->dispatch($event, MultiTenancyEvents::TENANT_REMOVED);
    }
}