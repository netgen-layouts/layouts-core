<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\EventListener\CsrfValidation;

use Netgen\Bundle\BlockManagerAdminBundle\EventListener\CsrfValidation\AdminCsrfValidationListener;
use Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class AdminCsrfValidationListenerTest extends TestCase
{
    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $csrfTokenManagerMock;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $sessionMock;

    /**
     * @var string
     */
    private $csrfTokenId;

    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\EventListener\CsrfValidation\AdminCsrfValidationListener
     */
    private $listener;

    public function setUp()
    {
        $this->csrfTokenManagerMock = $this->createMock(
            CsrfTokenManagerInterface::class
        );

        $this->sessionMock = $this->createMock(SessionInterface::class);

        $this->csrfTokenId = 'token_id';

        $this->listener = new AdminCsrfValidationListener(
            $this->csrfTokenManagerMock,
            $this->csrfTokenId
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            [KernelEvents::REQUEST => 'onKernelRequest'],
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\CsrfValidation\AdminCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::validateCsrfToken
     */
    public function testOnKernelRequest()
    {
        $this->csrfTokenManagerMock
            ->expects($this->once())
            ->method('isTokenValid')
            ->with($this->equalTo(new CsrfToken('token_id', 'token')))
            ->will($this->returnValue(true));

        $this->sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(true));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->headers->set('X-CSRF-Token', 'token');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\CsrfValidation\AdminCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::validateCsrfToken
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @expectedExceptionMessage Missing or invalid CSRF token
     */
    public function testOnKernelRequestThrowsAccessDeniedExceptionOnInvalidToken()
    {
        $this->csrfTokenManagerMock
            ->expects($this->once())
            ->method('isTokenValid')
            ->with($this->equalTo(new CsrfToken('token_id', 'token')))
            ->will($this->returnValue(false));

        $this->sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(true));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->headers->set('X-CSRF-Token', 'token');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\CsrfValidation\AdminCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::validateCsrfToken
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @expectedExceptionMessage Missing or invalid CSRF token
     */
    public function testOnKernelRequestThrowsAccessDeniedExceptionOnMissingTokenHeader()
    {
        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(true));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->setMethod(Request::METHOD_POST);
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\CsrfValidation\AdminCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\CsrfValidation\AdminCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestInNonAdminRequest()
    {
        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, false);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\CsrfValidation\AdminCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoSession()
    {
        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(false));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\CsrfValidation\AdminCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestWithSafeMethod()
    {
        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $this->sessionMock
            ->expects($this->once())
            ->method('isStarted')
            ->will($this->returnValue(true));

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->setMethod(Request::METHOD_GET);
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
