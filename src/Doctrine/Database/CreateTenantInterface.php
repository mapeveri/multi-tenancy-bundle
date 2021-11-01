<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database;

interface CreateTenantInterface
{
    public function create(string $dbName, int $tenantId): void;
}
