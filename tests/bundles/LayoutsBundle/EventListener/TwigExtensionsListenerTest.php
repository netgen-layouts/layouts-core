<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener;

use EdiModric\Twig\VersionExtension;
use Netgen\Bundle\LayoutsBundle\EventListener\TwigExtensionsListener;
use Netgen\Layouts\Tests\Utils\BackwardsCompatibility\CreateEventTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
use Twig\Extension\CoreExtension;

final class TwigExtensionsListenerTest extends TestCase
{
    use CreateEventTrait;

    private MockObject $twigMock;

    private TwigExtensionsListener $listener;

    protected function setUp(): void
    {
        $this->twigMock = $this->createMock(Environment::class);

        $this->listener = new TwigExtensionsListener(
            $this->twigMock,
            [
                CoreExtension::class,
                VersionExtension::class,
                stdClass::class,
                'NonExistent',
            ],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\TwigExtensionsListener::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\TwigExtensionsListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::REQUEST => 'onKernelRequest'],
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\TwigExtensionsListener::onKernelRequest
     */
    public function testOnKernelRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $this->twigMock
            ->method('hasExtension')
            ->withConsecutive(
                [self::identicalTo(CoreExtension::class)],
                [self::identicalTo(VersionExtension::class)],
            )
            ->willReturnOnConsecutiveCalls(true, false);

        $this->twigMock
            ->expects(self::once())
            ->method('addExtension')
            ->with(self::isInstanceOf(VersionExtension::class));

        $event = $this->createRequestEvent($kernelMock, $request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onKernelRequest($event);
    }
}
