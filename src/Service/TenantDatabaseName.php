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
     * @param string|null $fqdn
     * @return string
     */
    public function getName(?string $fqdn = ""): string
    {
        if ($fqdn) {
            $hostname =  $this->hostnameRepository->findOneBy(["fqdn" => $fqdn]);
        } else {
            $hostname = $this->hostnameRepository->findOneBy([]);
        }

        if (!$hostname) {
            throw new TenantNotFound();
        }

        return $hostname->getTenant()->getUuid();
    }
}
