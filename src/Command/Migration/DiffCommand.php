<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Command\Migration;

use MultiTenancyBundle\Service\TenantDatabaseName;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DiffCommand extends AbstractDoctrineCommand
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
            ->setName('tenancy:diff')
            ->setDescription('Wrapper to launch doctrine:migrations:diff command as it would require a "configuration" option')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $newInput = new ArrayInput([]);
        $newInput->setInteractive($input->isInteractive());

        $df = $this->getDependencyFactory($input);
        $diffCommand = new \Doctrine\Migrations\Tools\Console\Command\DiffCommand($df);

        // Set the first tenant connection
        $tenantDb = $this->tenantDatabaseName->getName();
        $this->setTenantConnection($df, $tenantDb);

        $diffCommand->run($newInput, $output);

        return 0;
    }
}
