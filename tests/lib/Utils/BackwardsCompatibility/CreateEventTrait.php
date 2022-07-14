<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Utils\BackwardsCompatibility;

use Exception;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use function class_exists;

/**
 * @deprecated Remove when support for Symfony 3.4 ends.
 *
 * Trait that enables test to use both deprecated events and new ones implemented in Symfony 4.3.
 */
trait CreateEventTrait
{
    /**
     * @return \Symfony\Component\HttpKernel\Event\RequestEvent
     */
    private function createRequestEvent(HttpKernelInterface $kernel, Request $request, int $requestType): object
    {
        if (class_exists(RequestEvent::class)) {
            return new RequestEvent($kernel, $request, $requestType);
        }

        if (class_exists(GetResponseEvent::class)) {
            return new GetResponseEvent($kernel, $request, $requestType);
        }

        throw new RuntimeException('Missing RequestEvent and GetResponseEvent classes');
    }

    /**
     * @return \Symfony\Component\HttpKernel\Event\ResponseEvent
     */
    private function createResponseEvent(HttpKernelInterface $kernel, Request $request, int $requestType, Response $response): object
    {
        if (class_exists(ResponseEvent::class)) {
            return new ResponseEvent($kernel, $request, $requestType, $response);
        }

        if (class_exists(FilterResponseEvent::class)) {
            return new FilterResponseEvent($kernel, $request, $requestType, $response);
        }

        throw new RuntimeException('Missing ResponseEvent and FilterResponseEvent classes');
    }

    /**
     * @param \Throwable|\Exception $throwable
     *
     * @return \Symfony\Component\HttpKernel\Event\ExceptionEvent
     */
    private function createExceptionEvent(HttpKernelInterface $kernel, Request $request, int $requestType, $throwable): object
    {
        if (class_exists(ExceptionEvent::class)) {
            return new ExceptionEvent($kernel, $request, $requestType, $throwable);
        }

        if ($throwable instanceof Exception && class_exists(GetResponseForExceptionEvent::class)) {
            return new GetResponseForExceptionEvent($kernel, $request, $requestType, $throwable);
        }

        throw new RuntimeException('Missing ExceptionEvent and GetResponseForExceptionEvent classes');
    }

    /**
     * @param mixed $controllerResult
     *
     * @return \Symfony\Component\HttpKernel\Event\ViewEvent
     */
    private function createViewEvent(HttpKernelInterface $kernel, Request $request, int $requestType, $controllerResult): object
    {
        if (class_exists(ViewEvent::class)) {
            return new ViewEvent($kernel, $request, $requestType, $controllerResult);
        }

        if (class_exists(GetResponseForControllerResultEvent::class)) {
            return new GetResponseForControllerResultEvent($kernel, $request, $requestType, $controllerResult);
        }

        throw new RuntimeException('Missing ViewEvent and GetResponseForControllerResultEvent classes');
    }

    /**
     * @return \Symfony\Component\HttpKernel\Event\TerminateEvent
     */
    private function createTerminateEvent(HttpKernelInterface $kernel, Request $request, Response $response): object
    {
        if (class_exists(TerminateEvent::class)) {
            return new TerminateEvent($kernel, $request, $response);
        }

        if (class_exists(PostResponseEvent::class)) {
            return new PostResponseEvent($kernel, $request, $response);
        }

        throw new RuntimeException('Missing TerminateEvent and PostResponseEvent classes');
    }
}
