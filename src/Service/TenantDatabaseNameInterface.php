<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Service;

interface TenantDatabaseNameInterface
{
    public function getName(?string $tenantName = ""): string;
}
