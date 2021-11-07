<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Command\Migration;

use Doctrine\Persistence\ManagerRegistry;
use MultiTenancyBundle\Doctrine\Database\Dialect\PostgreSql\PsqlUtils;
use MultiTenancyBundle\Doctrine\Database\Dialect\Driver;
use Symfony\Component\Console\Command\Command;
use Doctrine\Migrations\DependencyFactory as Df;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

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

    /**
     * @var Df
     */
    private $df;

    public function __construct(ManagerRegistry $registry, KernelInterface $appKernel, DependencyFactory $df)
    {
        parent::__construct();
        $this->registry = $registry;
        $this->appKernel = $appKernel;
        $this->df = $df;
    }

    protected function configure(): void
    {
        $this->addArgument('em', InputArgument::REQUIRED, 'Name of the Entity Manager to handle');
        $this->addOption('tenant', null, InputOption::VALUE_OPTIONAL);
    }

    protected function getDependencyFactory(InputInterface $input): Df
    {
        $emName = $input->getArgument('em');
        $em = $this->registry->getManager($emName);

        $projectDir = $this->appKernel->getProjectDir();

        return $this->df->create($em, $emName, $projectDir);
    }

    protected function setTenantConnection(Df $df, string $tenantDb): void
    {
        $tenantConnection = $df->getConnection();

        $driverName = Driver::getDriverName($tenantConnection);

        if (Driver::isPostgreSql($driverName)) {
            PsqlUtils::setSchema($tenantConnection, $tenantDb);
            return;
        }

        $tenantConnection->tenantConnect($tenantDb);
    }
}
