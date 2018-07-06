<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use EdiModric\Twig\VersionExtension;
use Netgen\Bundle\BlockManagerBundle\EventListener\TwigExtensionsListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
use Twig\Extensions\IntlExtension;

final class TwigExtensionsListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $twigMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\TwigExtensionsListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->twigMock = $this->createMock(Environment::class);

        $this->listener = new TwigExtensionsListener($this->twigMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\TwigExtensionsListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\TwigExtensionsListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $this->assertSame(
            [KernelEvents::REQUEST => 'onKernelRequest'],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\TwigExtensionsListener::onKernelRequest
     */
    public function testOnKernelRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $this->twigMock
            ->expects($this->at(0))
            ->method('hasExtension')
            ->with($this->identicalTo(IntlExtension::class))
            ->will($this->returnValue(false));

        $this->twigMock
            ->expects($this->at(1))
            ->method('addExtension')
            ->with($this->isInstanceOf(IntlExtension::class));

        $this->twigMock
            ->expects($this->at(2))
            ->method('hasExtension')
            ->with($this->identicalTo(VersionExtension::class))
            ->will($this->returnValue(false));

        $this->twigMock
            ->expects($this->at(3))
            ->method('addExtension')
            ->with($this->isInstanceOf(VersionExtension::class));

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\TwigExtensionsListener::onKernelRequest
     */
    public function testOnKernelRequestWithExtensionsExist(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $this->twigMock
            ->expects($this->at(0))
            ->method('hasExtension')
            ->with($this->identicalTo(IntlExtension::class))
            ->will($this->returnValue(true));

        $this->twigMock
            ->expects($this->at(1))
            ->method('hasExtension')
            ->with($this->identicalTo(VersionExtension::class))
            ->will($this->returnValue(true));

        $this->twigMock
            ->expects($this->never())
            ->method('addExtension');

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
