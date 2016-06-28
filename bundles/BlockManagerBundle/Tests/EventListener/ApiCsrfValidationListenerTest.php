<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ApiCsrfValidationListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $csrfTokenManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var string
     */
    protected $csrfTokenId;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener
     */
    protected $listener;

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

        $this->listener = new ApiCsrfValidationListener(
            $this->csrfTokenManagerMock,
            $this->csrfTokenId
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        self::assertEquals(
            array(KernelEvents::REQUEST => 'onKernelRequest'),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::validateCsrfToken
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::isMethodSafe
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
        $request->headers->set(ApiCsrfValidationListener::CSRF_TOKEN_HEADER, 'token');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::validateCsrfToken
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::isMethodSafe
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
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
        $request->headers->set(ApiCsrfValidationListener::CSRF_TOKEN_HEADER, 'token');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::validateCsrfToken
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::isMethodSafe
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
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
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestInNonApiRequest()
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
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoTokenManager()
    {
        $this->listener = new ApiCsrfValidationListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        self::assertNull($this->listener->onKernelRequest($event));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoTokenId()
    {
        $this->listener = new ApiCsrfValidationListener(
            $this->csrfTokenManagerMock
        );

        $this->csrfTokenManagerMock
            ->expects($this->never())
            ->method('isTokenValid');

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::onKernelRequest
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
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::onKernelRequest
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ApiCsrfValidationListener::isMethodSafe
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
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->setSession($this->sessionMock);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
