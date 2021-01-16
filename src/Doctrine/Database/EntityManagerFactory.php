<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\EventManager;

class EntityManagerFactory
{
    public function create(array $conn, Configuration $configuration, EventManager $eventManager)
    {
        return EntityManager::create($conn, $configuration, $eventManager);
    }
}
