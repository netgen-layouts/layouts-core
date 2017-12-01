<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\CsrfValidation;

use Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CsrfValidationListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $csrfTokenManagerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $sessionMock;

    /**
     * @var string
     */
    private $csrfTokenId;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener
     */
    private $listener;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->csrfTokenManagerMock = $this->createMock(
            CsrfTokenManagerInterface::class
        );

        $this->sessionMock = $this->createMock(SessionInterface::class);

        $this->csrfTokenId = 'token_id';

        $this->listener = new CsrfValidationListener(
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
            array(KernelEvents::REQUEST => 'onKernelRequest'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
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
        $request->headers->set(CsrfValidationListener::CSRF_TOKEN_HEADER, 'token');
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
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
        $request->headers->set(CsrfValidationListener::CSRF_TOKEN_HEADER, 'token');
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::__construct
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
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
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
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
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
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
