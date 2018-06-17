<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\CsrfValidation;

use Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\ApiCsrfValidationListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class ApiCsrfValidationListenerTest extends TestCase
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
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\ApiCsrfValidationListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->csrfTokenManagerMock = $this->createMock(
            CsrfTokenManagerInterface::class
        );

        $this->sessionMock = $this->createMock(SessionInterface::class);

        $this->csrfTokenId = 'token_id';

        $this->listener = new ApiCsrfValidationListener(
            $this->csrfTokenManagerMock,
            $this->csrfTokenId
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals(
            [KernelEvents::REQUEST => 'onKernelRequest'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\ApiCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::validateCsrfToken
     */
    public function testOnKernelRequest(): void
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
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\ApiCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::validateCsrfToken
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @expectedExceptionMessage Missing or invalid CSRF token
     */
    public function testOnKernelRequestThrowsAccessDeniedExceptionOnInvalidToken(): void
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
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\ApiCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::validateCsrfToken
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @expectedExceptionMessage Missing or invalid CSRF token
     */
    public function testOnKernelRequestThrowsAccessDeniedExceptionOnMissingTokenHeader(): void
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
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\ApiCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest(): void
    {
        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\ApiCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestInNonApiRequest(): void
    {
        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, false);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\ApiCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoSession(): void
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
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\ApiCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestWithSafeMethod(): void
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
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
