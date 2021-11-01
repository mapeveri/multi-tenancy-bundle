<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\DBAL;

interface TenantConnectionInterface
{
    public function tenantConnect(string $dbName): void;

    public function getDriverConnection(): string;
}
