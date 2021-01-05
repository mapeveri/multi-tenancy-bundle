<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Service;

interface TenantCreateDatabaseInterface
{
    public function create(string $dbName, int $tenantId): void;
}
