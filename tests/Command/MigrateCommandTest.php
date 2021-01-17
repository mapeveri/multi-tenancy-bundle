<?php

namespace MultiTenancyBundle\Tests\Command;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Doctrine\Migrations\Migrator;
use Doctrine\Migrations\QueryWriter;
use Doctrine\Migrations\DbalMigrator;
use Doctrine\Migrations\Finder\Finder;
use Doctrine\Migrations\Version\Version;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\DependencyFactory;
use Symfony\Component\Console\Application;
use Doctrine\Migrations\MigrationsRepository;
use Doctrine\Migrations\MigratorConfiguration;
use MultiTenancyBundle\Tests\Shared\TenantUtils;
use Doctrine\Migrations\Version\MigrationFactory;
use Symfony\Component\HttpKernel\KernelInterface;
use MultiTenancyBundle\Service\TenantDatabaseName;
use Doctrine\Migrations\Metadata\MigrationPlanList;
use MultiTenancyBundle\Repository\TenantRepository;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\Migrations\Configuration\Configuration;
use MultiTenancyBundle\Repository\HostnameRepository;
use Doctrine\Migrations\FilesystemMigrationsRepository;
use MultiTenancyBundle\Command\Migration\MigrateCommand;
use MultiTenancyBundle\Command\Migration\DependencyFactory as Df;
use MultiTenancyBundle\Tests\Shared\Command\Utils as UtilsCommand;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;

class MigrateCommandTest extends TestCase
{
    private $commandTester;
    private $hostnameRepository;
    private $dependencyFactory;

    protected function setUp()
    {
        $utils = new TenantUtils();
        $utilsCommand = new UtilsCommand();
        
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $em = $this->createMock(EntityManager::class);
        $kernel = $this->createMock(KernelInterface::class);
        $dfFactory = $this->createMock(Df::class);
        $migration = $this->createMock(AbstractMigration::class);
        $finder = $this->createMock(Finder::class);
        $factory = $this->createMock(MigrationFactory::class);
        $queryWriter = $this->createMock(QueryWriter::class);
        $tenantRepository = $this->createMock(TenantRepository::class);
        $this->hostnameRepository = $this->createMock(HostnameRepository::class);

        $metadataConfiguration = new TableMetadataStorageConfiguration();
        $configuration = new Configuration();
        $configuration->setMetadataStorageConfiguration($metadataConfiguration);

        $this->dependencyFactory = $utilsCommand->getDependencyFactory();
        $this->dependencyFactory->setService(QueryWriter::class, $queryWriter);
        $migrationRepository = new FilesystemMigrationsRepository([], [], $finder, $factory);
        $this->registerMigrationInstance($migrationRepository, new Version('A'), $migration);
        $this->dependencyFactory->setService(MigrationsRepository::class, $migrationRepository);

        $dfFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->dependencyFactory);

        $kernel->expects($this->any())
            ->method('getProjectDir')
            ->willReturn('');

        $managerRegistry->expects($this->any())
            ->method('getManager')
            ->willReturn($em);

        $this->hostnameRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($utils->getTenant());

        $tenantRepository->expects($this->any())
            ->method('findAll')
            ->willReturn($utils->getArrayTenants());

        $tenantDatabaseName = new TenantDatabaseName($this->hostnameRepository);
        $migrate = new MigrateCommand($managerRegistry, $kernel, $dfFactory);
        $migrate->setTenantDatabaseName($tenantDatabaseName);
        $migrate->setTenantRepository($tenantRepository);

        $application = new Application();
        $application->add($migrate);
        $command = $application->find('tenancy:migrate');
        $this->commandTester = new CommandTester($command);
    }
 
    public function testExecute()
    {
        $migrator = $this->createMock(DbalMigrator::class);
        $this->dependencyFactory->setService(Migrator::class, $migrator);

        $this->commandTester->setInputs(['yes']);

        $migrator->expects($this->any())
            ->method('migrate')
            ->willReturnCallback(static function (MigrationPlanList $planList, MigratorConfiguration $configuration): array {
                self::assertCount(1, $planList);
                self::assertEquals(new Version('A'), $planList->getFirst()->getVersion());

                return ['A'];
            });

        // Execute one tenant
        $this->commandTester->execute(['em' => "tenant", '--tenant' => "tenant1"]);

        self::assertSame(0, $this->commandTester->getStatusCode());
        self::assertStringContainsString('Executing tenant: 45b9d690-100c-4fa4-b133-996efdaf2499', trim($this->commandTester->getDisplay(true)));

        // Execute all tenants
        $this->commandTester->execute(['em' => "tenant"]);

        self::assertSame(0, $this->commandTester->getStatusCode());
        self::assertStringContainsString('Executing tenant: 45b9d690-100c-4fa4-b133-996efdaf2499', trim($this->commandTester->getDisplay(true)));
    }

    private function registerMigrationInstance(MigrationsRepository $repository, Version $version, AbstractMigration $migration): void
    {
        $reflection = new \ReflectionMethod(FilesystemMigrationsRepository::class, 'registerMigrationInstance');
        $reflection->setAccessible(true);
        $reflection->invoke($repository, $version, $migration);
    }
}
