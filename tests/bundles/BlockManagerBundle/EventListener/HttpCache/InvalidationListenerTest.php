<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener\HttpCache;

use Exception;
use FOS\HttpCache\Exception\ExceptionCollection;
use FOS\HttpCacheBundle\CacheManager;
use Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\InvalidationListener;
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

class InvalidationListenerTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheManagerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\InvalidationListener
     */
    protected $listener;

    public function setUp()
    {
        $this->cacheManagerMock = $this->createMock(CacheManager::class);

        $this->listener = new InvalidationListener($this->cacheManagerMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\InvalidationListener::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\InvalidationListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(
                KernelEvents::TERMINATE => 'onKernelTerminate',
                KernelEvents::EXCEPTION => 'onKernelException',
                ConsoleEvents::TERMINATE => 'onConsoleTerminate',
                ConsoleEvents::EXCEPTION => 'onConsoleTerminate',
            ),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\InvalidationListener::onKernelTerminate
     */
    public function testOnKernelTerminate()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new PostResponseEvent(
            $kernelMock,
            $request,
            new Response()
        );

        $this->cacheManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->listener->onKernelTerminate($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\InvalidationListener::onKernelTerminate
     */
    public function testOnKernelTerminateWithException()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new PostResponseEvent(
            $kernelMock,
            $request,
            new Response()
        );

        $this->cacheManagerMock
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new ExceptionCollection()));

        $this->listener->onKernelTerminate($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\InvalidationListener::onKernelException
     */
    public function testOnKernelException()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            new Response(),
            new Exception()
        );

        $this->cacheManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->listener->onKernelException($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\InvalidationListener::onKernelException
     */
    public function testOnKernelExceptionWithException()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            new Response(),
            new Exception()
        );

        $this->cacheManagerMock
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new ExceptionCollection()));

        $this->listener->onKernelException($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\InvalidationListener::onConsoleTerminate
     */
    public function testOnConsoleTerminate()
    {
        $commandMock = $this->createMock(Command::class);
        $inputMock = $this->createMock(InputInterface::class);
        $outputMock = $this->createConfiguredMock(
            OutputInterface::class,
            array(
                'getVerbosity' => OutputInterface::VERBOSITY_VERY_VERBOSE,
                'writeln' => 'Sent 2 invalidation request(s)',
            )
        );

        $event = new ConsoleEvent(
            $commandMock,
            $inputMock,
            $outputMock
        );

        $this->cacheManagerMock
            ->expects($this->once())
            ->method('flush')
            ->will($this->returnValue(2));

        $this->listener->onConsoleTerminate($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\InvalidationListener::onConsoleTerminate
     */
    public function testOnConsoleTerminateWithNonVerboseOutput()
    {
        $commandMock = $this->createMock(Command::class);
        $inputMock = $this->createMock(InputInterface::class);
        $outputMock = $this->createConfiguredMock(
            OutputInterface::class,
            array(
                'getVerbosity' => OutputInterface::VERBOSITY_QUIET,
            )
        );

        $event = new ConsoleEvent(
            $commandMock,
            $inputMock,
            $outputMock
        );

        $this->cacheManagerMock
            ->expects($this->once())
            ->method('flush')
            ->will($this->returnValue(2));

        $outputMock
            ->expects($this->never())
            ->method('writeln');

        $this->listener->onConsoleTerminate($event);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache\InvalidationListener::onConsoleTerminate
     */
    public function testOnConsoleTerminateWithNoFlushes()
    {
        $commandMock = $this->createMock(Command::class);
        $inputMock = $this->createMock(InputInterface::class);
        $outputMock = $this->createConfiguredMock(
            OutputInterface::class,
            array(
                'getVerbosity' => OutputInterface::VERBOSITY_VERY_VERBOSE,
            )
        );

        $event = new ConsoleEvent(
            $commandMock,
            $inputMock,
            $outputMock
        );

        $this->cacheManagerMock
            ->expects($this->once())
            ->method('flush')
            ->will($this->returnValue(0));

        $outputMock
            ->expects($this->never())
            ->method('writeln');

        $this->listener->onConsoleTerminate($event);
    }
}
