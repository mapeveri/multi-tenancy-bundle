<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database;

interface CreateDatabaseInterface
{
    public function createDatabase(string $dbName): void;

    public function createDatabaseUser(string $dbName, int $tenantId): void;
}
