<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Command\Migration;

use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Migrations\DependencyFactory as Df;
use MultiTenancyBundle\Doctrine\Migration\DirectoryMigration;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;

class DependencyFactory
{
    public function create(EntityManagerInterface $em, string $emName, string $projectDir): Df
    {
        // Define configurations
        $fileMigrations = Yaml::parseFile($projectDir . '/config/packages/doctrine_migrations.yaml');
        $config = DirectoryMigration::getConfiguration($emName, $fileMigrations);
        return Df::fromEntityManager($config, new ExistingEntityManager($em));
    }
}
