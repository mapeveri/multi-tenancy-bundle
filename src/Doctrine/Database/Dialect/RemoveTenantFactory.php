<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database\Dialect;

use MultiTenancyBundle\Doctrine\Database\Dialect\MySql\RemoveTenantMySql;
use MultiTenancyBundle\Doctrine\Database\Dialect\PostgreSql\RemoveTenantPsql;
use MultiTenancyBundle\Doctrine\Database\RemoveTenantInterface;
use MultiTenancyBundle\Doctrine\DBAL\TenantConnectionInterface;
use RuntimeException;

class RemoveTenantFactory
{
    /**
     * @var RemoveTenantMySql
     */
    private $removeTenantMySql;

    /**
     * @var RemoveTenantPsql
     */
    private $removeTenantPsql;

    /**
     * @required
     */
    public function setRemoveTenantMySql(RemoveTenantMySql $removeTenantMySql)
    {
        $this->removeTenantMySql = $removeTenantMySql;
    }

    /**
     * @required
     */
    public function setRemoveTenantPsql(RemoveTenantPsql $removeTenantPsql)
    {
        $this->removeTenantPsql = $removeTenantPsql;
    }

    public function __invoke(TenantConnectionInterface $tenantConnection): RemoveTenantInterface
    {
        switch($tenantConnection->getDriverConnection()) {
            case Driver::MYSQL:
                $service = $this->removeTenantMySql;
                break;
            case Driver::POSTGRESQL:
                $service = $this->removeTenantPsql;
                break;
            default:
                throw new RuntimeException('Invalid driver. Driver supported mysql and postgresql.');
        }

        return $service;
    }
}