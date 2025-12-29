<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use InvalidArgumentException as BaseInvalidArgumentException;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetIsAppRequestListener;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\ThrowableConversionListener;
use Netgen\Layouts\Exception\API\ConfigException;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\InvalidArgumentException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Exception\Validation\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Throwable;

#[CoversClass(ThrowableConversionListener::class)]
final class ThrowableConversionListenerTest extends TestCase
{
    private ThrowableConversionListener $listener;

    protected function setUp(): void
    {
        $this->listener = new ThrowableConversionListener();
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [ExceptionEvent::class => ['onException', 10]],
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @param class-string<\Symfony\Component\HttpKernel\Exception\HttpException> $convertedClass
     */
    #[DataProvider('onExceptionDataProvider')]
    public function testOnException(Throwable $throwable, string $convertedClass, int $statusCode, bool $converted): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsAppRequestListener::APP_API_FLAG_NAME, true);

        $event = new ExceptionEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $throwable,
        );

        $this->listener->onException($event);

        $eventThrowable = $event->getThrowable();

        self::assertInstanceOf($convertedClass, $eventThrowable);
        self::assertSame($throwable->getMessage(), $eventThrowable->getMessage());
        self::assertSame($throwable->getCode(), $eventThrowable->getCode());
        self::assertSame($statusCode, $eventThrowable->getStatusCode());

        $converted ?
            self::assertSame($throwable, $eventThrowable->getPrevious()) :
            self::assertSame($throwable, $eventThrowable);
    }

    public function testOnExceptionNotConvertsOtherExceptions(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsAppRequestListener::APP_API_FLAG_NAME, true);
        $throwable = new RuntimeException('Some error');

        $event = new ExceptionEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $throwable,
        );

        $this->listener->onException($event);

        $eventThrowable = $event->getThrowable();

        self::assertInstanceOf(RuntimeException::class, $eventThrowable);
    }

    public function testOnExceptionInSubRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsAppRequestListener::APP_API_FLAG_NAME, true);
        $throwable = new NotFoundException('param', 'Some error');

        $event = new ExceptionEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::SUB_REQUEST,
            $throwable,
        );

        $this->listener->onException($event);

        $eventThrowable = $event->getThrowable();

        self::assertSame($throwable, $eventThrowable);
    }

    public function testOnExceptionWithNonAPIRequest(): void
    {
        $kernelStub = self::createStub(HttpKernelInterface::class);
        $request = Request::create('/');
        $throwable = new NotFoundException('param', 'Some error');

        $event = new ExceptionEvent(
            $kernelStub,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $throwable,
        );

        $this->listener->onException($event);

        $eventThrowable = $event->getThrowable();

        self::assertSame($throwable, $eventThrowable);
    }

    /**
     * @return iterable<mixed>
     */
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
