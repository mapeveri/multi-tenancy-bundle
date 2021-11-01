<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\DBAL;

use Doctrine\DBAL\Connection;
use MultiTenancyBundle\Doctrine\Database\Dialect\PostgreSql\PsqlUtils;
use MultiTenancyBundle\Doctrine\Database\Dialect\Driver;
use ReflectionException;

class TenantConnectionWrapper extends Connection implements TenantConnectionInterface
{
    /**
     * Set the tenant connection
     *
     * @param string $dbName
     * @return void
     * @throws ReflectionException
     */
    public function tenantConnect(string $dbName): void
    {
        $this->close();

        $reflection = new \ReflectionObject($this);
        $refProperty = $reflection->getParentClass()->getProperty('params');
        $refProperty->setAccessible(true);

        $params = $refProperty->getValue($this);

        $driverName = $this->getDriverConnection();

        if (Driver::isPostgreSql($driverName)) {
            PsqlUtils::setSchema($this, $dbName);
        } else {
            $params['dbname'] = $dbName;
        }

        $refProperty->setValue($this, $params);
    }

    public function getDriverConnection(): string
    {
        return Driver::getDriverName($this);
    }
}
