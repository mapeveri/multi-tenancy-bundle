<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Service;

use MultiTenancyBundle\Doctrine\Database\CreateDatabaseInterface;

final class TenantCreateDatabase
{
    /**
     * @var CreateDatabaseInterface
     */
    private $createDatabase;

    public function __construct(CreateDatabaseInterface $createDatabase)
    {
        $this->createDatabase = $createDatabase;
    }

    /**
     * Create the schema on the database
     *
     * @param  string $dbName
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\Tools\ToolsException
     * @return void
     */
    public function create(string $dbName, int $tenantId): void
    {
        $this->createDatabase->createDatabase($dbName);
        $this->createDatabase->createDatabaseUser($dbName, $tenantId);
    }
}
