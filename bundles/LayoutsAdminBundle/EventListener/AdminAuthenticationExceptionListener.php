<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class AdminAuthenticationExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // Priority needs to be higher than built in exception listener
        return [ExceptionEvent::class => ['onException', 20]];
    }

    /**
     * Converts Symfony authentication exceptions to HTTP Access Denied exception.
     */
    public function onException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->attributes->getBoolean(SetIsAdminRequestListener::ADMIN_FLAG_NAME)) {
            return;
        }

        if (!$event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        $exception = $event->getThrowable();
        if (!$exception instanceof AuthenticationException && !$exception instanceof AccessDeniedException) {
            return;
        }

        $event->setThrowable(new AccessDeniedHttpException());
        $event->stopPropagation();
    }
}
