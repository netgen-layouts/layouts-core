<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Netgen\BlockManager\Exception\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Netgen\Bundle\BlockManagerBundle\Exception\InternalServerErrorHttpException;

class ExceptionConversionListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $exceptionMap = array(
        NotFoundException::class => NotFoundHttpException::class,
        InvalidArgumentException::class => BadRequestHttpException::class,
        BadStateException::class => UnprocessableEntityHttpException::class,
        Exception::class => InternalServerErrorHttpException::class,
        // Various other useful exceptions
        AccessDeniedException::class => AccessDeniedHttpException::class,
    );

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::EXCEPTION => array('onException', 10));
    }

    /**
     * Converts exceptions to Symfony HTTP exceptions.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onException(GetResponseForExceptionEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $exception = $event->getException();

        foreach ($this->exceptionMap as $sourceException => $targetException) {
            if (is_a($exception, $sourceException, true)) {
                $exceptionClass = $targetException;
                break;
            }
        }

        if (isset($exceptionClass)) {
            $convertedException = new $exceptionClass(
                $exception->getMessage(),
                $exception,
                $exception->getCode()
            );

            $event->setException($convertedException);
        }
    }
}
