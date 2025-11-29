<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\ApiCsrfValidationListener;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsApiRequestListener;
use Netgen\Bundle\LayoutsAdminBundle\Security\CsrfTokenValidatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(ApiCsrfValidationListener::class)]
final class ApiCsrfValidationListenerTest extends TestCase
{
    private MockObject&CsrfTokenValidatorInterface $csrfTokenValidatorMock;

    private string $csrfTokenId;

    private ApiCsrfValidationListener $listener;

    protected function setUp(): void
    {
        $this->csrfTokenValidatorMock = $this->createMock(
            CsrfTokenValidatorInterface::class,
        );

        $this->csrfTokenId = 'token_id';

        $this->listener = new ApiCsrfValidationListener(
            $this->csrfTokenValidatorMock,
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
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $this->csrfTokenValidatorMock
            ->expects($this->once())
            ->method('validateCsrfToken')
            ->with(self::identicalTo($request), self::identicalTo($this->csrfTokenId))
            ->willReturn(true);

        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $event = new RequestEvent($kernelMock, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestThrowsAccessDeniedExceptionOnInvalidToken(): void
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('Missing or invalid CSRF token');

        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $this->csrfTokenValidatorMock
            ->expects($this->once())
            ->method('validateCsrfToken')
            ->with(self::identicalTo($request), self::identicalTo($this->csrfTokenId))
            ->willReturn(false);

        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $event = new RequestEvent($kernelMock, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestInSubRequest(): void
    {
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $this->csrfTokenValidatorMock
            ->expects($this->never())
            ->method('validateCsrfToken');

        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $event = new RequestEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestInNonApiRequest(): void
    {
        $request = Request::create('/');

        $this->csrfTokenValidatorMock
            ->expects($this->never())
            ->method('validateCsrfToken');

        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $event = new RequestEvent($kernelMock, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
