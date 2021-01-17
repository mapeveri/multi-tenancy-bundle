<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\DBAL;

use Doctrine\DBAL\Connection;
use MultiTenancyBundle\Doctrine\DBAL\TenantConnectionInterface;

class TenantConnectionWrapper extends Connection implements TenantConnectionInterface
{
    /**
     * Set the tenant connection
     *
     * @return void
     */
    public function tenantConnect(string $dbName): void
    {
        $this->close();

        $reflection = new \ReflectionObject($this);
        $refProperty = $reflection->getParentClass()->getProperty('params');
        $refProperty->setAccessible(true);

        $params = $refProperty->getValue($this);
        $params['dbname'] = $dbName;

        $refProperty->setValue($this, $params);
    }
}
