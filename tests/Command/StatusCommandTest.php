<?php

namespace MultiTenancyBundle\Tests\Command;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Application;
use MultiTenancyBundle\Exception\TenantNotFound;
use MultiTenancyBundle\Tests\Shared\TenantUtils;
use Symfony\Component\HttpKernel\KernelInterface;
use MultiTenancyBundle\Service\TenantDatabaseName;
use MultiTenancyBundle\Tests\Shared\Command\Utils as UtilsCommand;
use Symfony\Component\Console\Tester\CommandTester;
use MultiTenancyBundle\Repository\HostnameRepository;
use MultiTenancyBundle\Command\Migration\StatusCommand;
use MultiTenancyBundle\Command\Migration\DependencyFactory;

class StatusCommandTest extends TestCase
{
    private $commandTester;
    private $hostnameRepository;

    protected function setUp()
    {
        $utils = new TenantUtils();
        $utilsCommand = new UtilsCommand();
        
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $em = $this->createMock(EntityManager::class);
        $kernel = $this->createMock(KernelInterface::class);
        $df = $this->createMock(DependencyFactory::class);
        $this->hostnameRepository = $this->createMock(HostnameRepository::class);

        $kernel->expects($this->any())
            ->method('getProjectDir')
            ->willReturn('');

        $managerRegistry->expects($this->any())
            ->method('getManager')
            ->willReturn($em);

        $this->hostnameRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($utils->getTenant());
        
        $df->expects($this->any())
            ->method('create')
            ->willReturn($utilsCommand->getDependencyFactory());

        $tenantDatabaseName = new TenantDatabaseName($this->hostnameRepository);
        $status = new StatusCommand($managerRegistry, $kernel, $df);
        $status->setTenantDatabaseName($tenantDatabaseName);
        
        $application = new Application();
        $application->add($status);
        $command = $application->find('tenancy:status');
        $this->commandTester = new CommandTester($command);
    }
 
    public function testExecuteException()
    {
        $this->expectException(TenantNotFound::class);
        $this->commandTester->execute(['em' => "tenant"]);
    }

    public function testExecute()
    {
        $this->commandTester->execute(['em' => "tenant", '--tenant' => "tenant1"]);
        $this->assertTrue(true);

        $lines = array_map('trim', explode("\n", trim($this->commandTester->getDisplay(true))));
        $this->assertSame(
            [
                '+----------------------+----------------------+------------------------------------------------------------------------+',
                '| Configuration                                                                                                        |',
                '+----------------------+----------------------+------------------------------------------------------------------------+',
                '| Storage              | Type                 | Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration |',
                '|                      | Table Name           | doctrine_migration_versions                                            |',
                '|                      | Column Name          | version                                                                |',
                '|----------------------------------------------------------------------------------------------------------------------|',
                '| Database             | Driver               | Doctrine\DBAL\Driver\PDO\SQLite\Driver                                 |',
                '|                      | Name                 |                                                                        |',
                '|----------------------------------------------------------------------------------------------------------------------|',
                '| Versions             | Previous             | 0                                                                      |',
                '|                      | Current              | 0                                                                      |',
                '|                      | Next                 | Already at latest version                                              |',
                '|                      | Latest               | 0                                                                      |',
                '|----------------------------------------------------------------------------------------------------------------------|',
                '| Migrations           | Executed             | 0                                                                      |',
                '|                      | Executed Unavailable | 0                                                                      |',
                '|                      | Available            | 0                                                                      |',
                '|                      | New                  | 0                                                                      |',
                '|----------------------------------------------------------------------------------------------------------------------|',
                '| Migration Namespaces | DoctrineMigrations   | /tmp                                                                   |',
                '+----------------------+----------------------+------------------------------------------------------------------------+',
            ],
            $lines
        );
    }
}
