<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\HttpCache;

use Exception;
use Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\InvalidationListener;
use Netgen\Layouts\HttpCache\ClientInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class InvalidationListenerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $httpCacheClientMock;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\InvalidationListener
     */
    private $listener;

    protected function setUp(): void
    {
        $this->httpCacheClientMock = $this->createMock(ClientInterface::class);

        $this->listener = new InvalidationListener($this->httpCacheClientMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\InvalidationListener::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\InvalidationListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [
                KernelEvents::TERMINATE => 'onKernelTerminate',
                KernelEvents::EXCEPTION => 'onKernelException',
                ConsoleEvents::TERMINATE => 'onConsoleTerminate',
                ConsoleEvents::ERROR => 'onConsoleTerminate',
            ],
            $this->listener::getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\InvalidationListener::onKernelTerminate
     */
    public function testOnKernelTerminate(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new PostResponseEvent(
            $kernelMock,
            $request,
            new Response()
        );

        $this->httpCacheClientMock
            ->expects(self::once())
            ->method('commit');

        $this->listener->onKernelTerminate($event);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\InvalidationListener::onKernelException
     */
    public function testOnKernelException(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Exception()
        );

        $this->httpCacheClientMock
            ->expects(self::once())
            ->method('commit');

        $this->listener->onKernelException($event);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\InvalidationListener::onConsoleTerminate
     */
    public function testOnConsoleTerminate(): void
    {
        $commandMock = $this->createMock(Command::class);
        $inputMock = $this->createMock(InputInterface::class);
        $outputMock = $this->createMock(OutputInterface::class);

        $event = new ConsoleEvent(
            $commandMock,
            $inputMock,
            $outputMock
        );

        $this->httpCacheClientMock
            ->expects(self::once())
            ->method('commit');

        $this->listener->onConsoleTerminate($event);
    }
}
