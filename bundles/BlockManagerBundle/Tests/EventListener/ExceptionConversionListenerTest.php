<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Netgen\BlockManager\API\Exception\NotFoundException;
use PHPUnit_Framework_TestCase;
use InvalidArgumentException;
use Exception;

class ExceptionConversionListenerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $eventListener = new ExceptionConversionListener();

        self::assertEquals(
            array(KernelEvents::EXCEPTION => array('onException', 10)),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionConvertsNotFoundException()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('/');
        $exception = new NotFoundException('what', 'identifier');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        self::assertInstanceOf(
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
            $event->getException()
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $event->getException()->getStatusCode());
        self::assertEquals($exception->getMessage(), $event->getException()->getMessage());
        self::assertEquals($exception->getCode(), $event->getException()->getCode());
        self::assertEquals($exception, $event->getException()->getPrevious());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionConvertsInvalidArgumentException()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('/');
        $exception = new InvalidArgumentException('Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        self::assertInstanceOf(
            'Symfony\Component\HttpKernel\Exception\BadRequestHttpException',
            $event->getException()
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $event->getException()->getStatusCode());
        self::assertEquals($exception->getMessage(), $event->getException()->getMessage());
        self::assertEquals($exception->getCode(), $event->getException()->getCode());
        self::assertEquals($exception, $event->getException()->getPrevious());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionConvertsOtherException()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = Request::create('/');
        $exception = new Exception('Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        self::assertInstanceOf(
            'Netgen\Bundle\BlockManagerBundle\Exception\InternalServerErrorHttpException',
            $event->getException()
        );

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $event->getException()->getStatusCode());
        self::assertEquals($exception->getMessage(), $event->getException()->getMessage());
        self::assertEquals($exception->getCode(), $event->getException()->getCode());
        self::assertEquals($exception, $event->getException()->getPrevious());
    }
}
