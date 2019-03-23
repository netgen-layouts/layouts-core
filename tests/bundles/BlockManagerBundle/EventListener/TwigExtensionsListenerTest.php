<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use EdiModric\Twig\VersionExtension;
use Netgen\Bundle\BlockManagerBundle\EventListener\TwigExtensionsListener;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
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

        $this->listener = new TwigExtensionsListener(
            $this->twigMock,
            [
                IntlExtension::class,
                VersionExtension::class,
                stdClass::class,
                'NonExistent',
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\TwigExtensionsListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\TwigExtensionsListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::REQUEST => ['onKernelRequest', Kernel::VERSION_ID < 30400 ? 1024 : 0]],
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
            ->expects(self::at(0))
            ->method('hasExtension')
            ->with(self::identicalTo(IntlExtension::class))
            ->will(self::returnValue(true));

        $this->twigMock
            ->expects(self::at(1))
            ->method('hasExtension')
            ->with(self::identicalTo(VersionExtension::class))
            ->will(self::returnValue(false));

        $this->twigMock
            ->expects(self::once())
            ->method('addExtension')
            ->with(self::isInstanceOf(VersionExtension::class));

        $event = new GetResponseEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
