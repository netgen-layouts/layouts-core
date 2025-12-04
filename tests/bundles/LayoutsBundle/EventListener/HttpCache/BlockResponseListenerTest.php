<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\HttpCache;

use Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\BlockResponseListener;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\HttpCache\TaggerInterface;
use Netgen\Layouts\View\View\BlockView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(BlockResponseListener::class)]
final class BlockResponseListenerTest extends TestCase
{
    private Stub&TaggerInterface $taggerStub;

    private BlockResponseListener $listener;

    protected function setUp(): void
    {
        $this->taggerStub = self::createStub(TaggerInterface::class);

        $this->listener = new BlockResponseListener($this->taggerStub);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [ResponseEvent::class => ['onKernelResponse', 10]],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnKernelResponse(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $block = new Block();
        $request->attributes->set('nglView', new BlockView($block));

        $event = new ResponseEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );

        $this->taggerStub
            ->method('tagBlock')
            ->with(self::identicalTo($block));

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithSubRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglView', new BlockView(new Block()));

        $event = new ResponseEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Response(),
        );

        $this->listener->onKernelResponse($event);
    }

    public function testOnKernelResponseWithoutSupportedValue(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('nglView', 42);

        $event = new ResponseEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Response(),
        );

        $this->listener->onKernelResponse($event);
    }
}
