<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsBundle\EventListener\ContextListener;
use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Context\ContextBuilderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(ContextListener::class)]
final class ContextListenerTest extends TestCase
{
    private Context $context;

    private Stub&ContextBuilderInterface $contextBuilderStub;

    private Stub&UriSigner $uriSignerStub;

    private ContextListener $listener;

    protected function setUp(): void
    {
        $this->context = new Context();

        $this->contextBuilderStub = self::createStub(ContextBuilderInterface::class);
        $this->uriSignerStub = self::createStub(UriSigner::class);

        $this->listener = new ContextListener(
            $this->context,
            $this->contextBuilderStub,
            $this->uriSignerStub,
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
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $this->contextBuilderStub
            ->method('buildContext')
            ->with(self::identicalTo($this->context));

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestWithContextFromAttributes(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglContext', ['var' => 'value']);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertSame(['var' => 'value'], $this->context->all());
    }

    public function testOnKernelRequestInSubRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->query->set('nglContext', ['var' => 'value']);

        $event = new RequestEvent($kernelStub, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertSame([], $this->context->all());
    }
}
