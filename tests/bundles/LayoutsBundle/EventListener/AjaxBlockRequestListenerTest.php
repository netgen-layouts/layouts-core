<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsBundle\EventListener\AjaxBlockRequestListener;
use Netgen\Layouts\Tests\Utils\BackwardsCompatibility\CreateEventTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class AjaxBlockRequestListenerTest extends TestCase
{
    use CreateEventTrait;

    private AjaxBlockRequestListener $listener;

    protected function setUp(): void
    {
        $this->listener = new AjaxBlockRequestListener();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\AjaxBlockRequestListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::REQUEST => ['onKernelRequest', 10]],
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\AjaxBlockRequestListener::onKernelRequest
     *
     * @dataProvider onKernelRequestDataProvider
     */
    public function testOnKernelRequest(string $uri, string $filteredUri): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create($uri);

        $request->attributes->set('_route', 'nglayouts_ajax_block');

        $event = $this->createRequestEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertTrue($event->getRequest()->attributes->has('nglContextUri'));
        self::assertSame($filteredUri, $event->getRequest()->attributes->get('nglContextUri'));
    }

    public function onKernelRequestDataProvider(): array
    {
        return [
            ['/test/uri', '/test/uri'],
            ['/test/uri?page=13', '/test/uri'],
            ['/test/uri?var=value&page=abc', '/test/uri?var=value&page=abc'],
            ['/test/uri?page=13&var=value', '/test/uri?var=value'],
            ['/test/uri?page=13&page=14', '/test/uri'],
            ['/test/uri?page=13&page=14&var=value', '/test/uri?var=value'],
            ['/test/uri?var=value&page=13', '/test/uri?var=value'],
            ['/test/uri?var=value&page=13&page=14', '/test/uri?var=value'],
            ['/test/uri?var=value&page=13&var2=value2&page=14', '/test/uri?var=value&var2=value2'],
        ];
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\AjaxBlockRequestListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = $this->createRequestEvent($kernelMock, $request, HttpKernelInterface::SUB_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('nglContextUri'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\AjaxBlockRequestListener::onKernelRequest
     */
    public function testOnKernelRequestWithInvalidRoute(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('_route', 'some_route');

        $event = $this->createRequestEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertFalse($event->getRequest()->attributes->has('nglContextUri'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\AjaxBlockRequestListener::onKernelRequest
     */
    public function testOnKernelRequestWithExistingContextUri(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $request->attributes->set('_route', 'nglayouts_ajax_block');
        $request->attributes->set('nglContextUri', '/some/uri');

        $event = $this->createRequestEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);

        self::assertSame('/some/uri', $event->getRequest()->attributes->get('nglContextUri'));
    }
}
