<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Netgen\BlockManager\Exception\ValidationFailedException;
use Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener;
use Netgen\Bundle\BlockManagerBundle\Exception\InternalServerErrorHttpException;
use Netgen\Bundle\BlockManagerBundle\Tests\EventListener\Stubs\ExceptionStub;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Exception\BadStateException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use RuntimeException;
use PHPUnit\Framework\TestCase;

class ExceptionConversionListenerTest extends TestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $eventListener = new ExceptionConversionListener();

        $this->assertEquals(
            array(KernelEvents::EXCEPTION => array('onException', 10)),
            $eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener::onException
     * @dataProvider onExceptionDataProvider
     *
     * @param \Exception $exception
     * @param string $convertedClass
     * @param int $statusCode
     */
    public function testOnException($exception, $convertedClass, $statusCode)
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        $this->assertInstanceOf(
            $convertedClass,
            $event->getException()
        );

        $this->assertEquals($statusCode, $event->getException()->getStatusCode());
        $this->assertEquals($exception->getMessage(), $event->getException()->getMessage());
        $this->assertEquals($exception->getCode(), $event->getException()->getCode());
        $this->assertEquals($exception, $event->getException()->getPrevious());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionNotConvertsOtherExceptions()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new RuntimeException('Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        $this->assertInstanceOf(
            RuntimeException::class,
            $event->getException()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionInSubRequest()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new NotFoundException('param', 'Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $exception
        );

        $eventListener->onException($event);

        $this->assertEquals($exception, $event->getException());
    }

    public function onExceptionDataProvider()
    {
        return array(
            array(
                new NotFoundException('param', 'Some error'),
                NotFoundHttpException::class,
                Response::HTTP_NOT_FOUND,
            ),
            array(
                new InvalidArgumentException('param', 'Some error'),
                BadRequestHttpException::class,
                Response::HTTP_BAD_REQUEST,
            ),
            array(
                new ValidationFailedException('Some error'),
                BadRequestHttpException::class,
                Response::HTTP_BAD_REQUEST,
            ),
            array(
                new BadStateException('param', 'Some error'),
                UnprocessableEntityHttpException::class,
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ),
            array(
                new ExceptionStub('Some error'),
                InternalServerErrorHttpException::class,
                Response::HTTP_INTERNAL_SERVER_ERROR,
            ),
            array(
                new AccessDeniedException('Some error'),
                AccessDeniedHttpException::class,
                Response::HTTP_FORBIDDEN,
            ),
        );
    }
}
