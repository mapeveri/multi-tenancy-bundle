<?php

namespace MultiTenancyBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use MultiTenancyBundle\Entity\Tenant;
use MultiTenancyBundle\Entity\Hostname;
use Symfony\Component\HttpFoundation\Request;
use MultiTenancyBundle\Exception\TenantNotFound;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use MultiTenancyBundle\Repository\HostnameRepository;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use MultiTenancyBundle\EventListener\TenantRequestListener;
use MultiTenancyBundle\Exception\TenantConnectionException;
use MultiTenancyBundle\Doctrine\DBAL\TenantConnectionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class TenantRequestListenerTest extends TestCase
{
    private $tenantConnectionMock;
    private $hostnameRepositoryMock;
    private $hostnameRepositoryMockData;
    private $kernelMock;
    private $fakeRequest;

    public function setUp()
    {
        $this->tenantConnectionMock = $this->createMock(TenantConnectionInterface::class);
        $this->tenantConnectionMock->expects($this->any())
            ->method('tenantConnect')
            ->willReturn(null);

        $this->hostnameRepositoryMock = $this->createMock(HostnameRepository::class);
        $this->hostnameRepositoryMock->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);
        
        $tenant = new Tenant();
        $tenant->setUuid('45b9d690-100c-4fa4-b133-996efdaf2499');

        $hostname = new Hostname();
        $hostname->setTenant($tenant);

        $this->hostnameRepositoryMockData = $this->createMock(HostnameRepository::class);
        $this->hostnameRepositoryMockData->expects($this->any())
            ->method('findOneBy')
            ->willReturn($hostname);

        $this->kernelMock = $this->createMock(HttpKernelInterface::class);

        $this->fakeRequest = Request::create('/', 'GET', [], [], [], [
            'HTTP_HOST'       => 'foo.domain.dev',
            'HTTP_USER_AGENT' => 'Symfony/2.0',
        ]);
        $this->fakeRequest->setSession(new Session(new MockArraySessionStorage()));
    }

    public function testOnKernelRequest()
    {
        $requestEvent = new RequestEvent($this->kernelMock, $this->fakeRequest, 0);

        $listener = new TenantRequestListener($this->tenantConnectionMock, $this->hostnameRepositoryMock);
        $listener->onKernelRequest($requestEvent);

        $this->assertTrue(true);
    }

    public function testOnKernelRequestMaster()
    {
        $requestEvent = new RequestEvent($this->kernelMock, $this->fakeRequest, 1);

        $listener = new TenantRequestListener($this->tenantConnectionMock, $this->hostnameRepositoryMockData);
        $listener->onKernelRequest($requestEvent);

        $this->assertTrue(true);
    }

    public function testOnKernelRequestMasterExceptionTenant()
    {
        $requestEvent = new RequestEvent($this->kernelMock, $this->fakeRequest, 1);

        $this->expectException(TenantNotFound::class);

        $listener = new TenantRequestListener($this->tenantConnectionMock, $this->hostnameRepositoryMock);
        $listener->onKernelRequest($requestEvent);
    }

    public function testOnKernelRequestMasterExceptionConnection()
    {
        $requestEvent = new RequestEvent($this->kernelMock, $this->fakeRequest, 1);
        
        $hostname = new Hostname();
        $hostnameRepositoryMockIncorrectData = $this->createMock(HostnameRepository::class);
        $hostnameRepositoryMockIncorrectData->expects($this->any())
            ->method('findOneBy')
            ->willReturn($hostname);
        
        $this->expectException(TenantConnectionException::class);

        $listener = new TenantRequestListener($this->tenantConnectionMock, $hostnameRepositoryMockIncorrectData);
        $listener->onKernelRequest($requestEvent);
    }
}
