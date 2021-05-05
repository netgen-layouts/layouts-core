<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\HttpCache;

use Exception;
use Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\InvalidationListener;
use Netgen\Layouts\HttpCache\InvalidatorInterface;
use Netgen\Layouts\Tests\Utils\BackwardsCompatibility\CreateEventTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class InvalidationListenerTest extends TestCase
{
    use CreateEventTrait;

    private MockObject $invalidatorMock;

    private InvalidationListener $listener;

    protected function setUp(): void
    {
        $this->invalidatorMock = $this->createMock(InvalidatorInterface::class);

        $this->listener = new InvalidationListener($this->invalidatorMock);
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
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\InvalidationListener::onKernelTerminate
     */
    public function testOnKernelTerminate(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = $this->createTerminateEvent(
            $kernelMock,
            $request,
            new Response(),
        );

        $this->invalidatorMock
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

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Exception(),
        );

        $this->invalidatorMock
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
            $outputMock,
        );

        $this->invalidatorMock
            ->expects(self::once())
            ->method('commit');

        $this->listener->onConsoleTerminate($event);
    }
}
