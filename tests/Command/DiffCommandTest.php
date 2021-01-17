<?php

namespace MultiTenancyBundle\Tests\Command;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Migrations\DependencyFactory;
use Symfony\Component\Console\Application;
use Doctrine\Migrations\Generator\DiffGenerator;
use MultiTenancyBundle\Tests\Shared\TenantUtils;
use Symfony\Component\HttpKernel\KernelInterface;
use MultiTenancyBundle\Service\TenantDatabaseName;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Generator\ClassNameGenerator;
use MultiTenancyBundle\Command\Migration\DiffCommand;
use MultiTenancyBundle\Repository\HostnameRepository;
use Doctrine\Migrations\Metadata\ExecutedMigrationsList;
use Doctrine\Migrations\Metadata\AvailableMigrationsList;
use Doctrine\Migrations\Version\MigrationStatusCalculator;
use MultiTenancyBundle\Command\Migration\DependencyFactory as Df;
use MultiTenancyBundle\Tests\Shared\Command\Utils as UtilsCommand;

class DiffCommandTest extends TestCase
{
    private $commandTester;
    private $hostnameRepository;
    private $migrationStatusCalculator;
    private $migrationDiffGenerator;
    private $classNameGenerator;

    protected function setUp()
    {
        $utils = new TenantUtils();
        $utilsCommand = new UtilsCommand();
        
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $em = $this->createMock(EntityManager::class);
        $kernel = $this->createMock(KernelInterface::class);
        $dfFactory = $this->createMock(Df::class);
        $dependencyFactory = $this->createMock(DependencyFactory::class);
        $this->migrationDiffGenerator = $this->createMock(DiffGenerator::class);
        $this->migrationStatusCalculator = $this->createMock(MigrationStatusCalculator::class);
        $this->hostnameRepository = $this->createMock(HostnameRepository::class);
        $this->classNameGenerator = $this->createMock(ClassNameGenerator::class);

        $kernel->expects($this->any())
            ->method('getProjectDir')
            ->willReturn('');

        $managerRegistry->expects($this->any())
            ->method('getManager')
            ->willReturn($em);

        $this->hostnameRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($utils->getTenant());
        
        $dfFactory->expects($this->any())
            ->method('create')
            ->willReturn($dependencyFactory);

        $configuration = new Configuration();
        $configuration->addMigrationsDirectory('FooNs', sys_get_temp_dir());

        $dependencyFactory->expects($this->any())
            ->method('getClassNameGenerator')
            ->willReturn($this->classNameGenerator);

        $dependencyFactory->expects($this->any())
            ->method('getConfiguration')
            ->willReturn($configuration);

        $dependencyFactory->expects($this->any())
            ->method('getDiffGenerator')
            ->willReturn($this->migrationDiffGenerator);

        $dependencyFactory->expects($this->any())
            ->method('getMigrationStatusCalculator')
            ->willReturn($this->migrationStatusCalculator);
        
        $dependencyFactory->expects($this->any())
            ->method('getConnection')
            ->willReturn($utilsCommand->getSqliteConnection());

        $tenantDatabaseName = new TenantDatabaseName($this->hostnameRepository);
        $diff = new DiffCommand($managerRegistry, $kernel, $dfFactory);
        $diff->setTenantDatabaseName($tenantDatabaseName);
        
        $application = new Application();
        $application->add($diff);
        $command = $application->find('tenancy:diff');
        $this->commandTester = new CommandTester($command);
    }
 
    public function testExecute()
    {
        $this->migrationStatusCalculator
            ->method('getNewMigrations')
            ->willReturn(new AvailableMigrationsList([]));

        $this->migrationStatusCalculator
            ->method('getExecutedUnavailableMigrations')
            ->willReturn(new ExecutedMigrationsList([]));

        $this->classNameGenerator->expects(self::once())
            ->method('generateClassName')
            ->with('FooNs')
            ->willReturn('FooNs\\Version1234');

        $this->migrationDiffGenerator->expects(self::once())
            ->method('generate')
            ->with('FooNs\\Version1234')
            ->willReturn('/path/to/migration.php');

        $this->commandTester->execute(['em' => "tenant"]);
        $output = $this->commandTester->getDisplay(true);

        self::assertSame([
            'Generated new migration class to "/path/to/migration.php"',
            '',
            'To run just this migration for testing purposes, you can use migrations:execute --up \'FooNs\\\\Version1234\'',
            '',
            'To revert the migration you can use migrations:execute --down \'FooNs\\\\Version1234\'',
        ], array_map('trim', explode("\n", trim($output))));
    }
}
