<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminAuthenticationExceptionListener;
use Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Exception;

class AdminAuthenticationExceptionListenerTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminAuthenticationExceptionListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $eventListener = new AdminAuthenticationExceptionListener();

        $this->assertEquals(
            array(KernelEvents::EXCEPTION => array('onException', 20)),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminAuthenticationExceptionListener::onException
     */
    public function testOnException()
    {
        $eventListener = new AdminAuthenticationExceptionListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new AuthenticationException()
        );

        $eventListener->onException($event);

        $this->assertInstanceOf(AccessDeniedHttpException::class, $event->getException());
        $this->assertTrue($event->isPropagationStopped());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminAuthenticationExceptionListener::onException
     */
    public function testOnExceptionWithWrongException()
    {
        $eventListener = new AdminAuthenticationExceptionListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Exception()
        );

        $eventListener->onException($event);

        $this->assertInstanceOf(Exception::class, $event->getException());
        $this->assertFalse($event->isPropagationStopped());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminAuthenticationExceptionListener::onException
     */
    public function testOnExceptionInNonAdminRequest()
    {
        $eventListener = new AdminAuthenticationExceptionListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Exception()
        );

        $eventListener->onException($event);

        $this->assertInstanceOf(Exception::class, $event->getException());
        $this->assertFalse($event->isPropagationStopped());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminAuthenticationExceptionListener::onException
     */
    public function testOnExceptionInNonXmlHttpRequest()
    {
        $eventListener = new AdminAuthenticationExceptionListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Exception()
        );

        $eventListener->onException($event);

        $this->assertInstanceOf(Exception::class, $event->getException());
        $this->assertFalse($event->isPropagationStopped());
    }
}
