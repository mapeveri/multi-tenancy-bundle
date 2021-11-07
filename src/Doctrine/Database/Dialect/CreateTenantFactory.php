<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database\Dialect;

use MultiTenancyBundle\Doctrine\Database\CreateTenantInterface;
use MultiTenancyBundle\Doctrine\Database\Dialect\MySql\CreateTenantMySql;
use MultiTenancyBundle\Doctrine\Database\Dialect\PostgreSql\CreateTenantPsql;
use MultiTenancyBundle\Doctrine\DBAL\TenantConnectionInterface;
use RuntimeException;

class CreateTenantFactory
{
    /**
     * @var CreateTenantMySql
     */
    private $createTenantMySql;

    /**
     * @var CreateTenantPsql
     */
    private $createTenantPsql;

    /**
     * @required
     */
    public function setCreateTenantMySql(CreateTenantMySql $createTenantMySql)
    {
        $this->createTenantMySql = $createTenantMySql;
    }

    /**
     * @required
     */
    public function setCreateTenantPsql(CreateTenantPsql $createTenantPsql)
    {
        $this->createTenantPsql = $createTenantPsql;
    }

    public function __invoke(TenantConnectionInterface $tenantConnection): CreateTenantInterface
    {
        switch($tenantConnection->getDriverConnection()) {
            case Driver::MYSQL:
                $service = $this->createTenantMySql;
                break;
            case Driver::POSTGRESQL:
                $service = $this->createTenantPsql;
                break;
            default:
                throw new RuntimeException('Invalid driver. Driver supported mysql and postgresql.');
        }

        return $service;
    }
}