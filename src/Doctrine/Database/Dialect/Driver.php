<?php

declare (strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database\Dialect;

use Doctrine\DBAL\Connection;

class Driver
{
    public const MYSQL = 'mysql';
    public const POSTGRESQL = 'postgresql';

    public static function getDriverName(Connection $connection): string
    {
        return $connection->getDatabasePlatform()->getName();
    }

    public static function isPostgreSql(string $driver): bool
    {
        return self::POSTGRESQL === $driver;
    }
}