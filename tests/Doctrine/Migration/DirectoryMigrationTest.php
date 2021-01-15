<?php

namespace MultiTenancyBundle\Tests\Doctrine\Migration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use MultiTenancyBundle\Doctrine\Migration\DirectoryMigration;
use MultiTenancyBundle\Exception\DirectoryMigrationException;

class DirectoryMigrationTest extends TestCase
{
    public function testGetConfiguration()
    {
        $fileMigrations = Yaml::parse("doctrine_migrations:
            migrations_paths:
                'DoctrineMigrations': 'migrations/Main'
                'DoctrineMigrationsTenant': 'migrations/Tenant'
        ");

        $config = (array)DirectoryMigration::getConfiguration("tenant", $fileMigrations);
        $value = reset($config);
        $this->assertEquals("migrations/Tenant", $value['migrations_paths']['DoctrineMigrationsTenant']);

        $config = (array)DirectoryMigration::getConfiguration("main", $fileMigrations);
        $value = reset($config);
        $this->assertEquals("migrations/Main", $value['migrations_paths']['DoctrineMigrations']);

        $config = (array)DirectoryMigration::getConfiguration("error", $fileMigrations);
        $this->assertEquals("migrations/Main", $value['migrations_paths']['DoctrineMigrations']);
    }

    public function testGetConfigurationInvalidFile()
    {
        $fileMigrations = [];

        $this->expectException(DirectoryMigrationException::class);
        DirectoryMigration::getConfiguration("tenant", $fileMigrations);
    }
}
