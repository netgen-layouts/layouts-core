<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Exception;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\AdminAuthenticationExceptionListener;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsAdminRequestListener;
use Netgen\Layouts\Tests\Utils\BackwardsCompatibility\CreateEventTrait;
use Netgen\Layouts\Utils\BackwardsCompatibility\ExceptionEventThrowableTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class AdminAuthenticationExceptionListenerTest extends TestCase
{
    use CreateEventTrait;
    use ExceptionEventThrowableTrait;

    private AdminAuthenticationExceptionListener $listener;

    protected function setUp(): void
    {
        $this->listener = new AdminAuthenticationExceptionListener();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\AdminAuthenticationExceptionListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::EXCEPTION => ['onException', 20]],
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\AdminAuthenticationExceptionListener::onException
     */
    public function testOnException(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new AuthenticationException(),
        );

        $this->listener->onException($event);

        $eventException = $this->getThrowable($event);

        self::assertInstanceOf(AccessDeniedHttpException::class, $eventException);
        self::assertTrue($event->isPropagationStopped());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\AdminAuthenticationExceptionListener::onException
     */
    public function testOnExceptionWithWrongException(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Exception(),
        );

        $this->listener->onException($event);

        $eventException = $this->getThrowable($event);

        self::assertNotInstanceOf(AccessDeniedHttpException::class, $eventException);
        self::assertFalse($event->isPropagationStopped());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\AdminAuthenticationExceptionListener::onException
     */
    public function testOnExceptionInNonAdminRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Exception(),
        );

        $this->listener->onException($event);

        $eventException = $this->getThrowable($event);

        self::assertNotInstanceOf(AccessDeniedHttpException::class, $eventException);
        self::assertFalse($event->isPropagationStopped());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\AdminAuthenticationExceptionListener::onException
     */
    public function testOnExceptionInNonXmlHttpRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Exception(),
        );

        $this->listener->onException($event);

        $eventException = $this->getThrowable($event);

        self::assertNotInstanceOf(AccessDeniedHttpException::class, $eventException);
        self::assertFalse($event->isPropagationStopped());
    }
}
