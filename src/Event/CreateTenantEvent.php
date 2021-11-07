<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class CreateTenantEvent extends Event
{
    protected $dbName;
    protected $tenantId;

    public function __construct(string $dbName, int $tenantId)
    {
        $this->dbName = $dbName;
        $this->tenantId = $tenantId;
    }

    public function dbName(): string
    {
        return $this->dbName;
    }

    public function tenantId(): int
    {
        return $this->tenantId;
    }
}