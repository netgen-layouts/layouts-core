<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener;
use Netgen\Bundle\BlockManagerBundle\Exception\InternalServerErrorHttpException;
use Netgen\Bundle\BlockManagerBundle\Tests\Stubs\NonAPIException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Exception\BadStateException;
use Netgen\Bundle\BlockManagerBundle\Tests\Stubs\Exception;

class ExceptionConversionListenerTest extends \PHPUnit_Framework_TestCase
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

        $kernelMock = $this->getMock(HttpKernelInterface::class);
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
            NotFoundHttpException::class,
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

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new InvalidArgumentException('what', 'identifier');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        self::assertInstanceOf(
            BadRequestHttpException::class,
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
    public function testOnExceptionConvertsBadStateException()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new BadStateException('param', 'Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        self::assertInstanceOf(
            UnprocessableEntityHttpException::class,
            $event->getException()
        );

        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $event->getException()->getStatusCode());
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

        $kernelMock = $this->getMock(HttpKernelInterface::class);
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
            InternalServerErrorHttpException::class,
            $event->getException()
        );

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $event->getException()->getStatusCode());
        self::assertEquals($exception->getMessage(), $event->getException()->getMessage());
        self::assertEquals($exception->getCode(), $event->getException()->getCode());
        self::assertEquals($exception, $event->getException()->getPrevious());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionNotConvertsNonAPIException()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new NonAPIException('Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        self::assertInstanceOf(
            NonAPIException::class,
            $event->getException()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionInSubRequest()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new Exception('Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        self::assertEquals($exception, $event->getException());
    }
}
