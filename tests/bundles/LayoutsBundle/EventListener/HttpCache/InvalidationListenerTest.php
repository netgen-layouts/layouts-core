<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\HttpCache;

use Exception;
use Netgen\Bundle\LayoutsBundle\EventListener\HttpCache\InvalidationListener;
use Netgen\Layouts\HttpCache\InvalidatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
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
    private Stub&InvalidatorInterface $invalidatorStub;

    private InvalidationListener $listener;

    protected function setUp(): void
    {
        $this->invalidatorStub = self::createStub(InvalidatorInterface::class);

        $this->listener = new InvalidationListener($this->invalidatorStub);
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
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new TerminateEvent(
            $kernelStub,
            $request,
            new Response(),
        );

        $this->invalidatorStub
            ->method('commit');

        $this->listener->onKernelTerminate($event);
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

        $this->invalidatorStub
            ->method('commit');

        $this->listener->onKernelException($event);
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

        $this->invalidatorStub
            ->method('commit');

        $this->listener->onConsoleTerminate($event);
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

        $this->invalidatorStub
            ->method('commit');

        $this->listener->onConsoleError($event);
    }
}
