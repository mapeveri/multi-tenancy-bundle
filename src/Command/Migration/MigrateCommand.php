<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Command\Migration;

use Exception;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand as DoctrineMigrateCommand;
use MultiTenancyBundle\Repository\TenantRepository;
use MultiTenancyBundle\Service\TenantDatabaseName;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MigrateCommand extends AbstractDoctrineCommand
{
    /**
     * @var TenantRepository
     */
    private $tenantRepository;

    /**
     * @var TenantDatabaseName
     */
    private $tenantDatabaseName;

    /**
     * @required
     */
    public function setTenantRepository(TenantRepository $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
    }

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
            ->setName('tenancy:migrate')
            ->setDescription('Wrapper to launch doctrine:migrations:migrate command as it would require a "configuration" option')
            ->addArgument('version', InputArgument::OPTIONAL, 'The version number (YYYYMMDDHHMMSS) or alias (first, prev, next, latest) to migrate to.', 'latest')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Execute the migration as a dry run.')
            ->addOption('query-time', null, InputOption::VALUE_NONE, 'Time all the queries individually.')
            ->addOption('allow-no-migration', null, InputOption::VALUE_NONE, 'Don\'t throw an exception if no migration is available (CI).')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tenant = $input->getOption('tenant');
        
        if ($tenant) {
            $tenantDb = $this->tenantDatabaseName->getName($tenant);
            $this->migrate($input, $output, $tenantDb);
        } else {
            $this->executeAllTenants($input, $output);
        }

        return 0;
    }

    /**
     * Execute all tenants migration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    private function executeAllTenants(InputInterface $input, OutputInterface $output): void
    {
        // Get array with all tenants name
        $tenants = $this->tenantRepository->findAll();

        foreach ($tenants as $tenant) {
            try {
                $this->migrate($input, $output, $tenant->getUuid());
            } catch (Exception $e) {
                $output->writeln("{$e->getMessage()}");
            }
        }
    }

    /**
     * Execute the migration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $tenantDb
     * @return void
     */
    private function migrate(InputInterface $input, OutputInterface $output, string $tenantDb): void
    {
        $df = $this->getDependencyFactory($input);
        $migrateCommand = new DoctrineMigrateCommand($df);

        $newInput = new ArrayInput([
            'version'               => $input->getArgument('version'),
            '--dry-run'             => $input->getOption('dry-run'),
            '--query-time'          => $input->getOption('query-time'),
            '--allow-no-migration'  => $input->getOption('allow-no-migration'),
        ]);
        $newInput->setInteractive(false);

        $output->writeln("<info>Executing tenant: {$tenantDb}</info>");
        $this->setTenantConnection($df, $tenantDb);

        // Execute the migration
        $migrateCommand->run($newInput, $output);
    }
}
