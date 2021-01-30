<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database;

interface CreateDatabaseInterface
{
    public function create(string $dbName): void;

    public function createUser(string $dbName, int $tenantId): void;
}
