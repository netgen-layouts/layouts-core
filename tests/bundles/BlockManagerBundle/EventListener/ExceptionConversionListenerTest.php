<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\EventListener;

use Exception;
use InvalidArgumentException as BaseInvalidArgumentException;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\Core\ConfigException;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Exception\Validation\ValidationException;
use Netgen\BlockManager\Exception\View\ViewException;
use Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use Netgen\Bundle\BlockManagerBundle\Tests\EventListener\Stubs\ExceptionStub;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * @param bool $converted
     */
    public function testOnException($exception, $convertedClass, $statusCode, $converted)
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

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

        $this->assertEquals($exception->getMessage(), $event->getException()->getMessage());
        $this->assertEquals($exception->getCode(), $event->getException()->getCode());

        if ($event->getException() instanceof HttpExceptionInterface) {
            $this->assertEquals($statusCode, $event->getException()->getStatusCode());
        }

        $converted ?
            $this->assertEquals($exception, $event->getException()->getPrevious()) :
            $this->assertNull($event->getException()->getPrevious());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionNotConvertsOtherExceptions()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
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
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
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

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionWithNonAPIRequest()
    {
        $eventListener = new ExceptionConversionListener();

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new NotFoundException('param', 'Some error');

        $event = new GetResponseForExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
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
                true,
            ),
            array(
                new InvalidArgumentException('param', 'Some error'),
                BadRequestHttpException::class,
                Response::HTTP_BAD_REQUEST,
                true,
            ),
            array(
                new BadStateException('param', 'Some error'),
                UnprocessableEntityHttpException::class,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                true,
            ),
            array(
                new ValidationException('Some error'),
                BadRequestHttpException::class,
                Response::HTTP_BAD_REQUEST,
                true,
            ),
            array(
                new ConfigException('Some error'),
                BadRequestHttpException::class,
                Response::HTTP_BAD_REQUEST,
                true,
            ),
            array(
                new ExceptionStub('Some error'),
                ExceptionStub::class,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                false,
            ),
            array(
                new Exception('Some error'),
                Exception::class,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                false,
            ),
            array(
                new AccessDeniedException('Some error'),
                AccessDeniedHttpException::class,
                Response::HTTP_FORBIDDEN,
                true,
            ),
            array(
                new BaseInvalidArgumentException('Some error'),
                BadRequestHttpException::class,
                Response::HTTP_BAD_REQUEST,
                true,
            ),
            array(
                new ViewException('Some error'),
                ViewException::class,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                false,
            ),
            array(
                new RuntimeException('Some error'),
                RuntimeException::class,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                false,
            ),
            array(
                new AccessDeniedHttpException('Some error'),
                AccessDeniedHttpException::class,
                Response::HTTP_FORBIDDEN,
                false,
            ),
        );
    }
}
