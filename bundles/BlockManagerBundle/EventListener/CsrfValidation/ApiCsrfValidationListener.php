<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation;

use Netgen\Bundle\BlockManagerBundle\EventListener\SetIsApiRequestListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ApiCsrfValidationListener extends CsrfValidationListener
{
    /**
     * This method validates CSRF token if CSRF protection is enabled.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException If token is invalid
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsApiRequestListener::API_FLAG_NAME) !== true) {
            return;
        }

        parent::onKernelRequest($event);
    }
}
