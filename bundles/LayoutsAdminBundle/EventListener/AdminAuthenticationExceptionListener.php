<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Netgen\Layouts\Utils\BackwardsCompatibility\ExceptionEventThrowableTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class AdminAuthenticationExceptionListener implements EventSubscriberInterface
{
    use ExceptionEventThrowableTrait;

    public static function getSubscribedEvents(): array
    {
        // Priority needs to be higher than built in exception listener
        return [KernelEvents::EXCEPTION => ['onException', 20]];
    }

    /**
     * Converts Symfony authentication exceptions to HTTP Access Denied exception.
     *
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
     */
    public function onException($event): void
    {
        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsAdminRequestListener::ADMIN_FLAG_NAME) !== true) {
            return;
        }

        if (!$event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        $exception = $this->getThrowable($event);
        if (!$exception instanceof AuthenticationException && !$exception instanceof AccessDeniedException) {
            return;
        }

        $this->setThrowable($event, new AccessDeniedHttpException());
        $event->stopPropagation();
    }
}
