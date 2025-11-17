<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\HttpCache;

use Exception;
use Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\InvalidationListener;
use Netgen\Layouts\HttpCache\InvalidatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(InvalidationListener::class)]
final class InvalidationListenerTest extends TestCase
{
    private MockObject&InvalidatorInterface $invalidatorMock;

    private InvalidationListener $listener;

    protected function setUp(): void
    {
        $this->invalidatorMock = $this->createMock(InvalidatorInterface::class);

        $this->listener = new InvalidationListener($this->invalidatorMock);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [
                TerminateEvent::class => 'onKernelTerminate',
                ExceptionEvent::class => 'onKernelException',
                ConsoleTerminateEvent::class => 'onConsoleTerminate',
                ConsoleErrorEvent::class => 'onConsoleError',
            ],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnKernelTerminate(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new TerminateEvent(
            $kernelMock,
            $request,
            new Response(),
        );

        $this->invalidatorMock
            ->expects(self::once())
            ->method('commit');

        $this->listener->onKernelTerminate($event);
    }

    public function testOnKernelException(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new ExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Exception(),
        );

        $this->invalidatorMock
            ->expects(self::once())
            ->method('commit');

        $this->listener->onKernelException($event);
    }

    public function testOnConsoleTerminate(): void
    {
        $commandMock = $this->createMock(Command::class);
        $inputMock = $this->createMock(InputInterface::class);
        $outputMock = $this->createMock(OutputInterface::class);

        $event = new ConsoleTerminateEvent(
            $commandMock,
            $inputMock,
            $outputMock,
            Command::SUCCESS,
        );

        $this->invalidatorMock
            ->expects(self::once())
            ->method('commit');

        $this->listener->onConsoleTerminate($event);
    }

    public function testOnConsoleError(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $outputMock = $this->createMock(OutputInterface::class);

        $event = new ConsoleErrorEvent(
            $inputMock,
            $outputMock,
            new Exception(),
        );

        $this->invalidatorMock
            ->expects(self::once())
            ->method('commit');

        $this->listener->onConsoleError($event);
    }
}
