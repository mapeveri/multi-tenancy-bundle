<?php

declare(strict_types=1);

namespace MultiTenancyBundle\Doctrine\Database;

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManagerInterface;

class CreateSchemaFactory
{
    public function create(EntityManagerInterface $em, array $meta)
    {
        $tool = new SchemaTool($em);
        $tool->createSchema($meta);
    }
}
