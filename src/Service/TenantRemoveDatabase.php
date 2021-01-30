<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Service;

use MultiTenancyBundle\Doctrine\Database\RemoveDatabaseInterface;

final class TenantRemoveDatabase
{
    /**
     * @var RemoveDatabaseInterface
     */
    private $removeDatabase;

    public function __construct(RemoveDatabaseInterface $removeDatabase)
    {
        $this->removeDatabase = $removeDatabase;
    }

    /**
     * Remove the schema on the database
     *
     * @param  string $dbName
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\Tools\ToolsException
     * @return void
     */
    public function remove(string $dbName, int $tenantId): void
    {
        $this->removeDatabase->remove($dbName);
        $this->removeDatabase->removeUser($dbName, $tenantId);
    }
}
