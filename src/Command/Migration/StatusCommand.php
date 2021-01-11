<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Command\Migration;

use MultiTenancyBundle\Exception\TenantNotFound;
use MultiTenancyBundle\Service\TenantDatabaseName;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class StatusCommand extends AbstractDoctrineCommand
{
    /**
     * @var TenantDatabaseName
     */
    private $tenantDatabaseName;

    /**
     * @required
     */
    public function setTenantDatabaseName(TenantDatabaseName $tenantDatabaseName)
    {
        $this->tenantDatabaseName = $tenantDatabaseName;
    }

    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('tenancy:status')
            ->setDescription('Wrapper to launch doctrine:migrations:status command as it would require a "configuration" option')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tenant = $input->getOption('tenant');

        if (!$tenant) {
            throw new TenantNotFound("The tenant parameter is mandatory");
        }

        $df = $this->getDependencyFactory($input);
        $tenantDb = $this->tenantDatabaseName->getName($tenant);
        $this->setTenantConnection($df, $tenantDb);

        $newInput = new ArrayInput([]);
        $statusCommand = new \Doctrine\Migrations\Tools\Console\Command\StatusCommand($df);
        $statusCommand->run($newInput, $output);

        return 0;
    }
}
