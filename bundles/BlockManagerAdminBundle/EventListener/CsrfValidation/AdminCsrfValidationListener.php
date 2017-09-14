<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\EventListener\CsrfValidation;

use Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetIsAdminRequestListener;
use Netgen\Bundle\BlockManagerBundle\EventListener\CsrfValidation\CsrfValidationListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class AdminCsrfValidationListener extends CsrfValidationListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $attributes = $event->getRequest()->attributes;
        if ($attributes->get(SetIsAdminRequestListener::ADMIN_FLAG_NAME) !== true) {
            return;
        }

        parent::onKernelRequest($event);
    }
}
