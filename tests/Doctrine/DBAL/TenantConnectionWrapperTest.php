<?php

namespace MultiTenancyBundle\Tests\Doctrine\DBAL;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\Configuration;
use Doctrine\Common\EventManager;
use MultiTenancyBundle\Doctrine\DBAL\TenantConnectionWrapper;

class TenantConnectionWrapperTest extends TestCase
{
    /**
     * @var array
     */
    protected $params = [
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'password',
        'port' => '1234',
        'dbname' => 'main'
    ];

    public function testTenantConnect()
    {
        $listenerMock = $this->createMock(ConnectDispatchEventListener::class);
        $platform = $this->createMock(AbstractPlatform::class);
        $listenerMock->expects($this->once())->method('postConnect');

        $eventManager = new EventManager();
        $eventManager->addEventListener([Events::postConnect], $listenerMock);

        $driverMock = $this->createMock(Driver::class);
        $driverMock->expects($this->once())
            ->method('connect');

        $driverMock->expects($this->once())
            ->method('getDatabasePlatform')
            ->will($this->returnValue($platform));

        $platform->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('mysql'));

        $tenantConnectionWrapper = new TenantConnectionWrapper($this->params, $driverMock, new Configuration(), $eventManager);
        $tenantConnectionWrapper->tenantConnect("databaseName");
        $tenantConnectionWrapper->connect();
        
        $this->assertEquals("databaseName", $tenantConnectionWrapper->getParams()['dbname']);
    }
}

interface ConnectDispatchEventListener
{
    public function postConnect(): void;
}