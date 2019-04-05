<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminCsrfValidationListener;
use Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener;
use Netgen\Bundle\BlockManagerBundle\Security\CsrfTokenValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class AdminCsrfValidationListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Security\CsrfTokenValidatorInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    private $csrfTokenValidatorMock;

    /**
     * @var string
     */
    private $csrfTokenId;

    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminCsrfValidationListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->csrfTokenValidatorMock = $this->createMock(
            CsrfTokenValidatorInterface::class
        );

        $this->csrfTokenId = 'token_id';

        $this->listener = new AdminCsrfValidationListener(
            $this->csrfTokenValidatorMock,
            $this->csrfTokenId
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminCsrfValidationListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::REQUEST => 'onKernelRequest'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminCsrfValidationListener::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminCsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequest(): void
    {
        $request = Request::create('/');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);

        $this->csrfTokenValidatorMock
            ->expects(self::once())
            ->method('validateCsrfToken')
            ->with(self::identicalTo($request), self::identicalTo($this->csrfTokenId))
            ->willReturn(true);

        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminCsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestThrowsAccessDeniedExceptionOnInvalidToken(): void
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('Missing or invalid CSRF token');

        $request = Request::create('/');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);

        $this->csrfTokenValidatorMock
            ->expects(self::once())
            ->method('validateCsrfToken')
            ->with(self::identicalTo($request), self::identicalTo($this->csrfTokenId))
            ->willReturn(false);

        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminCsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest(): void
    {
        $request = Request::create('/');
        $request->attributes->set(SetIsAdminRequestListener::ADMIN_FLAG_NAME, true);

        $this->csrfTokenValidatorMock
            ->expects(self::never())
            ->method('validateCsrfToken');

        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\AdminCsrfValidationListener::onKernelRequest
     */
    public function testOnKernelRequestInNonApiRequest(): void
    {
        $request = Request::create('/');

        $this->csrfTokenValidatorMock
            ->expects(self::never())
            ->method('validateCsrfToken');

        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
