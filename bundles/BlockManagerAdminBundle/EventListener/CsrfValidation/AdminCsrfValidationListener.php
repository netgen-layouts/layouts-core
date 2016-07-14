<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\EventListener\CsrfValidation;

use Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener;
use Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class AdminCsrfValidationListener extends CsrfValidationListener
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
        if ($attributes->get(SetIsAdminRequestListener::ADMIN_FLAG_NAME) !== true) {
            return;
        }

        parent::onKernelRequest($event);
    }
}
