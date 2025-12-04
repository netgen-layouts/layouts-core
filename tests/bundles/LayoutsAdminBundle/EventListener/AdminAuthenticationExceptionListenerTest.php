<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Exception;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\AdminAuthenticationExceptionListener;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsAdminRequestListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

#[CoversClass(AdminAuthenticationExceptionListener::class)]
final class AdminAuthenticationExceptionListenerTest extends TestCase
{
    private AdminAuthenticationExceptionListener $listener;

    protected function setUp(): void
    {
        $this->listener = new AdminAuthenticationExceptionListener();
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [ExceptionEvent::class => ['onException', 20]],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnException(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);

        $event = new ExceptionEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new AuthenticationException(),
        );

        $this->listener->onException($event);

        $eventException = $event->getThrowable();

        self::assertInstanceOf(AccessDeniedHttpException::class, $eventException);
        self::assertTrue($event->isPropagationStopped());
    }

    public function testOnExceptionWithWrongException(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);

        $event = new ExceptionEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Exception(),
        );

        $this->listener->onException($event);

        $eventException = $event->getThrowable();

        self::assertNotInstanceOf(AccessDeniedHttpException::class, $eventException);
        self::assertFalse($event->isPropagationStopped());
    }

    public function testOnExceptionInNonAdminRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $event = new ExceptionEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Exception(),
        );

        $this->listener->onException($event);

        $eventException = $event->getThrowable();

        self::assertNotInstanceOf(AccessDeniedHttpException::class, $eventException);
        self::assertFalse($event->isPropagationStopped());
    }

    public function testOnExceptionInNonXmlHttpRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);

        $event = new ExceptionEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Exception(),
        );

        $this->listener->onException($event);

        $eventException = $event->getThrowable();

        self::assertNotInstanceOf(AccessDeniedHttpException::class, $eventException);
        self::assertFalse($event->isPropagationStopped());
    }
}
