<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsBundle\EventListener\ContextListener;
use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Context\ContextBuilderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

#[CoversClass(ContextListener::class)]
final class ContextListenerTest extends TestCase
{
    private Context $context;

    private MockObject&ContextBuilderInterface $contextBuilderMock;

    private MockObject&UriSigner $uriSignerMock;

    private ContextListener $listener;

    protected function setUp(): void
    {
        $this->context = new Context();

        $this->contextBuilderMock = $this->createMock(ContextBuilderInterface::class);
        $this->uriSignerMock = $this->createMock(UriSigner::class);

        $this->listener = new ContextListener(
            $this->context,
            $this->contextBuilderMock,
            $this->uriSignerMock,
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::REQUEST => 'onKernelRequest'],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnKernelRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $this->contextBuilderMock
            ->expects(self::once())
            ->method('buildContext')
            ->with(self::identicalTo($this->context));

        $this->uriSignerMock
            ->expects(self::never())
            ->method('check');

        $event = new RequestEvent($kernelMock, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestWithContextFromAttributes(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglContext', ['var' => 'value']);

        $this->contextBuilderMock
            ->expects(self::never())
            ->method('buildContext');

        $this->uriSignerMock
            ->expects(self::never())
            ->method('check');

        $event = new RequestEvent($kernelMock, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertSame(['var' => 'value'], $this->context->all());
    }

    public function testOnKernelRequestInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->query->set('nglContext', ['var' => 'value']);

        $this->contextBuilderMock
            ->expects(self::never())
            ->method('buildContext');

        $event = new RequestEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertSame([], $this->context->all());
    }
}
