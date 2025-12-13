<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use InvalidArgumentException as BaseInvalidArgumentException;
use Netgen\Layouts\Exception\BadStateException;
use Netgen\Layouts\Exception\InvalidArgumentException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use function array_find;

final class ThrowableConversionListener implements EventSubscriberInterface
{
    /**
     * @var array<class-string<\Throwable>, class-string<\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface>>
     */
    private array $throwableMap = [
        NotFoundException::class => NotFoundHttpException::class,
        InvalidArgumentException::class => BadRequestHttpException::class,
        ValidationException::class => BadRequestHttpException::class,
        BadStateException::class => UnprocessableEntityHttpException::class,
        // Various other useful exceptions
        AccessDeniedException::class => AccessDeniedHttpException::class,
        BaseInvalidArgumentException::class => BadRequestHttpException::class,
    ];

    public static function getSubscribedEvents(): array
    {
        return [ExceptionEvent::class => ['onException', 10]];
    }

    /**
     * Converts exceptions to Symfony HTTP exceptions.
     */
    public function onException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->attributes->getBoolean(SetIsApiRequestListener::API_FLAG_NAME)) {
            return;
        }

        $throwable = $event->getThrowable();
        if ($throwable instanceof HttpExceptionInterface) {
            return;
        }

        $throwableClass = array_find(
            $this->throwableMap,
            static fn (string $targetThrowable, string $sourceThrowable): bool => $throwable instanceof $sourceThrowable,
        );

        if ($throwableClass !== null) {
            $convertedThrowable = new $throwableClass(
                $throwable->getMessage(),
                $throwable,
                $throwable->getCode(),
            );

            $event->setThrowable($convertedThrowable);
        }
    }
}
