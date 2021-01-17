<?php

namespace MultiTenancyBundle\Tests\Shared\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;

class Utils
{
    public function getSqliteConnection(): Connection
    {
        $params = ['driver' => 'pdo_sqlite', 'memory' => true, 'wrapperClass' => 'MultiTenancyBundle\Doctrine\DBAL\TenantConnectionWrapper'];

        return DriverManager::getConnection($params);
    }

    public function getDependencyFactory(): DependencyFactory
    {
        $configuration = new Configuration();
        $configuration->setMetadataStorageConfiguration(new TableMetadataStorageConfiguration());
        $configuration->addMigrationsDirectory('DoctrineMigrations', sys_get_temp_dir());

        $conn = $this->getSqliteConnection();

        return DependencyFactory::fromConnection(
            new ExistingConfiguration($configuration),
            new ExistingConnection($conn)
        );
    }
}
