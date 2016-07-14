<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SetIsAdminRequestListener implements EventSubscriberInterface
{
    const ADMIN_FLAG_NAME = 'ngbm_is_admin_request';
    const ADMIN_ROUTE_PREFIX = 'ngbm_admin_';

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => array('onKernelRequest', 30));
    }

    /**
     * Sets the {@link self::ADMIN_FLAG_NAME} flag if this is a request in admin interface.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route');
        if (stripos($currentRoute, self::ADMIN_ROUTE_PREFIX) !== 0) {
            return;
        }

        $request->attributes->set(self::ADMIN_FLAG_NAME, true);
    }
}
