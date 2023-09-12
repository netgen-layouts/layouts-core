<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Exception;
use InvalidArgumentException as BaseInvalidArgumentException;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionConversionListener;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsApiRequestListener;
use Netgen\Layouts\Exception\API\ConfigException;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\InvalidArgumentException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Tests\Utils\BackwardsCompatibility\CreateEventTrait;
use Netgen\Layouts\Utils\BackwardsCompatibility\ExceptionEventThrowableTrait;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ExceptionConversionListenerTest extends TestCase
{
    use CreateEventTrait;
    use ExceptionEventThrowableTrait;

    private ExceptionConversionListener $listener;

    protected function setUp(): void
    {
        $this->listener = new ExceptionConversionListener();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionConversionListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [KernelEvents::EXCEPTION => ['onException', 10]],
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @param class-string<\Symfony\Component\HttpKernel\Exception\HttpException> $convertedClass
     *
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionConversionListener::onException
     *
     * @dataProvider onExceptionDataProvider
     */
    public function testOnException(Exception $exception, string $convertedClass, int $statusCode, bool $converted): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception,
        );

        $this->listener->onException($event);

        $eventException = $this->getThrowable($event);

        self::assertInstanceOf($convertedClass, $eventException);
        self::assertSame($exception->getMessage(), $eventException->getMessage());
        self::assertSame($exception->getCode(), $eventException->getCode());
        self::assertSame($statusCode, $eventException->getStatusCode());

        $converted ?
            self::assertSame($exception, $eventException->getPrevious()) :
            self::assertSame($exception, $eventException);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionNotConvertsOtherExceptions(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $exception = new RuntimeException('Some error');

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception,
        );

        $this->listener->onException($event);

        $eventException = $this->getThrowable($event);

        self::assertInstanceOf(RuntimeException::class, $eventException);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionInSubRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $exception = new NotFoundException('param', 'Some error');

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $exception,
        );

        $this->listener->onException($event);

        $eventException = $this->getThrowable($event);

        self::assertSame($exception, $eventException);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\EventListener\ExceptionConversionListener::onException
     */
    public function testOnExceptionWithNonAPIRequest(): void
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $exception = new NotFoundException('param', 'Some error');

        $event = $this->createExceptionEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $exception,
        );

        $this->listener->onException($event);

        $eventException = $this->getThrowable($event);

        self::assertSame($exception, $eventException);
    }

    public static function onExceptionDataProvider(): iterable
    {
        return [
            [
                new NotFoundException('param', 'Some error'),
                NotFoundHttpException::class,
                Response::HTTP_NOT_FOUND,
                true,
            ],
            [
                new InvalidArgumentException('param', 'Some error'),
                BadRequestHttpException::class,
                Response::HTTP_BAD_REQUEST,
                true,
            ],
            [
                new BadStateException('param', 'Some error'),
                UnprocessableEntityHttpException::class,
                Response::HTTP_UNPROCESSABLE_ENTITY,
                true,
            ],
            [
                new ValidationException('Some error'),
                BadRequestHttpException::class,
                Response::HTTP_BAD_REQUEST,
                true,
            ],
            [
                new ConfigException('Some error'),
                BadRequestHttpException::class,
                Response::HTTP_BAD_REQUEST,
                true,
            ],
            [
                new AccessDeniedException('Some error'),
                AccessDeniedHttpException::class,
                Response::HTTP_FORBIDDEN,
                true,
            ],
            [
                new BaseInvalidArgumentException('Some error'),
                BadRequestHttpException::class,
                Response::HTTP_BAD_REQUEST,
                true,
            ],
            [
                new AccessDeniedHttpException('Some error'),
                AccessDeniedHttpException::class,
                Response::HTTP_FORBIDDEN,
                false,
            ],
        ];
    }
}
