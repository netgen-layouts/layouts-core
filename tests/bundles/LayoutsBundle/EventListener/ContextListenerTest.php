<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsBundle\EventListener\ContextListener;
use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Context\ContextBuilderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(ContextListener::class)]
final class ContextListenerTest extends TestCase
{
    private Context $context;

    protected function setUp(): void
    {
        $this->context = new Context();
    }

    public function testGetSubscribedEvents(): void
    {
        $listener = new ContextListener(
            $this->context,
            self::createStub(ContextBuilderInterface::class),
            self::createStub(UriSigner::class),
        );

        self::assertSame(
            [RequestEvent::class => 'onKernelRequest'],
            $listener::getSubscribedEvents(),
        );
    }

    public function testOnKernelRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $contextBuilderMock = $this->createMock(ContextBuilderInterface::class);
        $contextBuilderMock
            ->expects($this->once())
            ->method('buildContext')
            ->with(self::identicalTo($this->context));

        $listener = new ContextListener(
            $this->context,
            $contextBuilderMock,
            self::createStub(UriSigner::class),
        );

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $listener->onKernelRequest($event);
    }

    public function testOnKernelRequestWithContextFromAttributes(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglContext', ['var' => 'value']);

        $listener = new ContextListener(
            $this->context,
            self::createStub(ContextBuilderInterface::class),
            self::createStub(UriSigner::class),
        );

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $listener->onKernelRequest($event);

        self::assertSame(['var' => 'value'], $this->context->all());
    }

    public function testOnKernelRequestInSubRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->query->set('nglContext', ['var' => 'value']);

        $listener = new ContextListener(
            $this->context,
            self::createStub(ContextBuilderInterface::class),
            self::createStub(UriSigner::class),
        );

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::SUB_REQUEST);
        $listener->onKernelRequest($event);

        self::assertSame([], $this->context->all());
    }
}
