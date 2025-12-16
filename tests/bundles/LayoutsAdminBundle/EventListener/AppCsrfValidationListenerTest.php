<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\AppCsrfValidationListener;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsAppRequestListener;
use Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(AppCsrfValidationListener::class)]
final class AppCsrfValidationListenerTest extends TestCase
{
    private Stub&CsrfTokenValidatorInterface $csrfTokenValidatorStub;

    private string $csrfTokenId;

    private AppCsrfValidationListener $listener;

    protected function setUp(): void
    {
        $this->csrfTokenValidatorStub = self::createStub(CsrfTokenValidatorInterface::class);

        $this->csrfTokenId = 'token_id';

        $this->listener = new AppCsrfValidationListener(
            $this->csrfTokenValidatorStub,
            $this->csrfTokenId,
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [RequestEvent::class => 'onKernelRequest'],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnKernelRequest(): void
    {
        $request = Request::create('/');
        $request->attributes->set(SetIsAppRequestListener::APP_FLAG_NAME, true);

        $this->csrfTokenValidatorStub
            ->method('validateCsrfToken')
            ->with(self::identicalTo($request), self::identicalTo($this->csrfTokenId))
            ->willReturn(true);

        $kernelStub = self::createStub(HttpKernelInterface::class);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestThrowsAccessDeniedExceptionOnInvalidToken(): void
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('Missing or invalid CSRF token');

        $request = Request::create('/');
        $request->attributes->set(SetIsAppRequestListener::APP_FLAG_NAME, true);

        $this->csrfTokenValidatorStub
            ->method('validateCsrfToken')
            ->with(self::identicalTo($request), self::identicalTo($this->csrfTokenId))
            ->willReturn(false);

        $kernelStub = self::createStub(HttpKernelInterface::class);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestInSubRequest(): void
    {
        $request = Request::create('/');
        $request->attributes->set(SetIsAppRequestListener::APP_FLAG_NAME, true);

        $kernelStub = self::createStub(HttpKernelInterface::class);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::SUB_REQUEST);

        $csrfTokenValidatorMock = $this->createMock(CsrfTokenValidatorInterface::class);
        $csrfTokenValidatorMock
            ->expects($this->never())
            ->method('validateCsrfToken');

        $this->listener = new AppCsrfValidationListener(
            $csrfTokenValidatorMock,
            $this->csrfTokenId,
        );

        $this->listener->onKernelRequest($event);
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

        $this->listener = new AppCsrfValidationListener(
            $csrfTokenValidatorMock,
            $this->csrfTokenId,
        );

        $this->listener->onKernelRequest($event);
    }
}
