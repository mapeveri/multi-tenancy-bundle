<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Migration;

use Throwable;
use function strtolower;
use MultiTenancyBundle\Exception\DirectoryMigrationException;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;

final class DirectoryMigration
{
    /**
     * Get config doctrine migrations
     *
     * @param string $entityManagerName
     * @param array $fileMigrations
     * @return ConfigurationArray
     */
    public static function getConfiguration(string $entityManagerName, array $fileMigrations): ConfigurationArray
    {
        try {
            $path = $fileMigrations['doctrine_migrations']['migrations_paths'];

            if (strtolower($entityManagerName) === "tenant") {
                $migration = [
                    'DoctrineMigrationsTenant' => $path['DoctrineMigrationsTenant']
                ];
            } else {
                $migration = [
                    'DoctrineMigrations' => $path['DoctrineMigrations']
                ];
            }

            $config = new ConfigurationArray([
                'migrations_paths' => $migration,
            ]);
        } catch (Throwable $e) {
            throw new DirectoryMigrationException("The doctrine_migrations.yaml file is invalid.");
        }

        return $config;
    }
}
