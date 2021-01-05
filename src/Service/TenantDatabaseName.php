<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Service;

use MultiTenancyBundle\Exception\TenantNotFound;
use MultiTenancyBundle\Repository\HostnameRepository;
use MultiTenancyBundle\Service\TenantDatabaseNameInterface;

final class TenantDatabaseName implements TenantDatabaseNameInterface
{
    /**
     * @var HostnameRepository
     */
    private $hostnameRepository;

    public function __construct(HostnameRepository $hostnameRepository)
    {
        $this->hostnameRepository = $hostnameRepository;
    }

    /**
     * Get uuid name database
     *
     * @param string|null $tenantName
     * @return string
     */
    public function getName(?string $tenantName = ""): string
    {
        if ($tenantName) {
            $tenant =  $this->hostnameRepository->findOneBy(["fqdn" => $tenantName]);
        } else {
            $tenant = $this->hostnameRepository->findOneBy([]);
        }

        if (!$tenant) {
            throw new TenantNotFound();
        }

        return $tenant->getTenant()->getUuid();
    }
}
