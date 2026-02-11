<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\AppCsrfValidationListener;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsAppRequestListener;
use Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(AppCsrfValidationListener::class)]
final class AppCsrfValidationListenerTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $listener = new AppCsrfValidationListener(
            self::createStub(CsrfTokenValidatorInterface::class),
            'token_id',
        );

        self::assertSame(
            [RequestEvent::class => 'onKernelRequest'],
            $listener::getSubscribedEvents(),
        );
    }

    public function testOnKernelRequest(): void
    {
        $request = Request::create('/');
        $request->attributes->set(SetIsAppRequestListener::APP_API_FLAG_NAME, true);

        $csrfTokenValidatorMock = $this->createMock(CsrfTokenValidatorInterface::class);
        $csrfTokenValidatorMock
            ->expects($this->once())
            ->method('validateCsrfToken')
            ->willReturn(true);

        $listener = new AppCsrfValidationListener(
            $csrfTokenValidatorMock,
            'token_id',
        );

        $kernelStub = self::createStub(HttpKernelInterface::class);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $listener->onKernelRequest($event);
    }

    public function testOnKernelRequestThrowsAccessDeniedExceptionOnInvalidToken(): void
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('Missing or invalid CSRF token');

        $request = Request::create('/');
        $request->attributes->set(SetIsAppRequestListener::APP_API_FLAG_NAME, true);

        $csrfTokenValidatorMock = $this->createMock(CsrfTokenValidatorInterface::class);
        $csrfTokenValidatorMock
            ->expects($this->once())
            ->method('validateCsrfToken')
            ->willReturn(false);

        $listener = new AppCsrfValidationListener(
            $csrfTokenValidatorMock,
            'token_id',
        );

        $kernelStub = self::createStub(HttpKernelInterface::class);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $listener->onKernelRequest($event);
    }

    public function testOnKernelRequestInSubRequest(): void
    {
        $request = Request::create('/');
        $request->attributes->set(SetIsAppRequestListener::APP_API_FLAG_NAME, true);

        $kernelStub = self::createStub(HttpKernelInterface::class);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::SUB_REQUEST);

        $csrfTokenValidatorMock = $this->createMock(CsrfTokenValidatorInterface::class);
        $csrfTokenValidatorMock
            ->expects($this->never())
            ->method('validateCsrfToken');

        $listener = new AppCsrfValidationListener(
            $csrfTokenValidatorMock,
            'token_id',
        );

        $listener->onKernelRequest($event);
    }

    public function testOnKernelRequestInNonApiRequest(): void
    {
        $request = Request::create('/');

        $kernelStub = self::createStub(HttpKernelInterface::class);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);

        $csrfTokenValidatorMock = $this->createMock(CsrfTokenValidatorInterface::class);
        $csrfTokenValidatorMock
            ->expects($this->never())
            ->method('validateCsrfToken');

        $listener = new AppCsrfValidationListener(
            $csrfTokenValidatorMock,
            'token_id',
        );

        $listener->onKernelRequest($event);
    }
}
