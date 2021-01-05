<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Command\Migration;

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Persistence\ManagerRegistry;
use MultiTenancyBundle\Doctrine\Migration\DirectoryMigration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractDoctrineCommand extends Command
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var KernelInterface
     */
    private $appKernel;

    public function __construct(ManagerRegistry $registry, KernelInterface $appKernel)
    {
        parent::__construct();
        $this->registry = $registry;
        $this->appKernel = $appKernel;
    }

    protected function configure(): void
    {
        $this->addArgument('em', InputArgument::REQUIRED, 'Name of the Entity Manager to handle');
        $this->addOption('tenant', null, InputOption::VALUE_OPTIONAL);
    }

    protected function getDependencyFactory(InputInterface $input): DependencyFactory
    {
        $em = $this->registry->getManager($input->getArgument('em'));

        // Get configuration
        $projectDir = $this->appKernel->getProjectDir();
        $fileMigrations = Yaml::parseFile($projectDir . '/config/packages/doctrine_migrations.yaml');

        // Define configurations
        $config = DirectoryMigration::getConfiguration($input->getArgument('em'), $fileMigrations);
        return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($em));
    }

    protected function setTenantConnection(DependencyFactory $df, string $tenantDb): void
    {
        $tenantConnection = $df->getConnection();
        $tenantConnection->tenantConnect($tenantDb);
    }
}
