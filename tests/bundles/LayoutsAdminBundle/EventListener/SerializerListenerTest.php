<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\EventListener\SerializerListener;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsAppRequestListener;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\Tests\API\Stubs\Value as APIValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[CoversClass(SerializerListener::class)]
final class SerializerListenerTest extends TestCase
{
    private Stub&SerializerInterface $serializerStub;

    private SerializerListener $listener;

    protected function setUp(): void
    {
        $this->serializerStub = self::createStub(SerializerInterface::class);

        $this->listener = new SerializerListener($this->serializerStub);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [ViewEvent::class => 'onView'],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnView(): void
    {
        $value = new Value(new APIValue());

        $this->serializerStub
            ->method('serialize')
            ->willReturn('serialized content');

        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsAppRequestListener::APP_FLAG_NAME, true);

        $event = new ViewEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $value,
        );

        $this->listener->onView($event);

        self::assertInstanceOf(
            JsonResponse::class,
            $event->getResponse(),
        );

        self::assertSame(
            'serialized content',
            $event->getResponse()->getContent(),
        );
    }

    public function testOnViewWithNoHtmlRendering(): void
    {
        $value = new Value(new APIValue());

        $this->serializerStub
            ->method('serialize')
            ->willReturn('serialized content');

        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->query->set('html', 'false');
        $request->attributes->set(SetIsAppRequestListener::APP_FLAG_NAME, true);

        $event = new ViewEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $value,
        );

        $this->listener->onView($event);

        self::assertInstanceOf(
            JsonResponse::class,
            $event->getResponse(),
        );

        self::assertSame(
            'serialized content',
            $event->getResponse()->getContent(),
        );
    }

    public function testOnViewInSubRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new ViewEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            new Value(new APIValue()),
        );

        $this->listener->onView($event);

        self::assertFalse($event->hasResponse());
    }

    public function testOnViewWithoutSupportedValue(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsAppRequestListener::APP_FLAG_NAME, true);

        $event = new ViewEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            42,
        );

        $this->listener->onView($event);

        self::assertFalse($event->hasResponse());
    }
}
