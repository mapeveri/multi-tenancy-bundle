<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database;

interface RemoveTenantInterface
{
    public function remove(string $dbName, int $tenantId): void;
}
