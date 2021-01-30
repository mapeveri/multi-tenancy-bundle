<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database;

interface RemoveDatabaseInterface
{
    public function remove(string $dbName): void;

    public function removeUser(string $dbName, int $tenantId): void;
}
