<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\HttpCache;

use Exception;
use Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\InvalidationListener;
use Netgen\Layouts\HttpCache\InvalidatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
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
    public function testGetSubscribedEvents(): void
    {
        $listener = new InvalidationListener(self::createStub(InvalidatorInterface::class));

        self::assertSame(
            [
                TerminateEvent::class => 'onKernelTerminate',
                ExceptionEvent::class => 'onKernelException',
                ConsoleTerminateEvent::class => 'onConsoleTerminate',
                ConsoleErrorEvent::class => 'onConsoleError',
            ],
            $listener::getSubscribedEvents(),
        );
    }

    public function testOnKernelTerminate(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new TerminateEvent(
            $kernelStub,
            $request,
            new Response(),
        );

        $invalidatorMock = $this->createMock(InvalidatorInterface::class);
        $invalidatorMock
            ->expects($this->once())
            ->method('commit');

        $listener = new InvalidationListener($invalidatorMock);

        $listener->onKernelTerminate($event);
    }

    public function testOnKernelException(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new ExceptionEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            new Exception(),
        );

        $invalidatorMock = $this->createMock(InvalidatorInterface::class);
        $invalidatorMock
            ->expects($this->once())
            ->method('commit');

        $listener = new InvalidationListener($invalidatorMock);

        $listener->onKernelException($event);
    }

    public function testOnConsoleTerminate(): void
    {
        $commandStub = self::createStub(Command::class);
        $inputStub = self::createStub(InputInterface::class);
        $outputStub = self::createStub(OutputInterface::class);

        $event = new ConsoleTerminateEvent(
            $commandStub,
            $inputStub,
            $outputStub,
            Command::SUCCESS,
        );

        $invalidatorMock = $this->createMock(InvalidatorInterface::class);
        $invalidatorMock
            ->expects($this->once())
            ->method('commit');

        $listener = new InvalidationListener($invalidatorMock);

        $listener->onConsoleTerminate($event);
    }

    public function testOnConsoleError(): void
    {
        $inputStub = self::createStub(InputInterface::class);
        $outputStub = self::createStub(OutputInterface::class);

        $event = new ConsoleErrorEvent(
            $inputStub,
            $outputStub,
            new Exception(),
        );

        $invalidatorMock = $this->createMock(InvalidatorInterface::class);
        $invalidatorMock
            ->expects($this->once())
            ->method('commit');

        $listener = new InvalidationListener($invalidatorMock);

        $listener->onConsoleError($event);
    }
}
