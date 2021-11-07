<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database;

trait TenantConnectionTrait
{
    /**
     * Get tenant connection parameters
     *
     * @param string $dbName
     * @param array $params
     * @return array
     */
    public function getParamsConnectionTenant(string $dbName, array $params): array
    {
        return [
            'driver' => $params['driver'],
            'host' => $params['host'],
            'user' => $params['user'],
            'password' => $params['password'],
            'dbname' => $dbName
        ];
    }
}
