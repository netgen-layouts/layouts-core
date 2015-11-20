<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SetIsApiRequestListener implements EventSubscriberInterface
{
    const API_FLAG_NAME = 'ngbm_is_api_request';
    const API_ROUTE_PREFIX = 'netgen_block_manager_api_';

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => 'onKernelRequest');
    }

    /**
     * Sets the {@link self::API_FLAG_NAME} flag if this is a REST API request.
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
        if (stripos($currentRoute, self::API_ROUTE_PREFIX) !== 0) {
            return;
        }

        $request->attributes->set(self::API_FLAG_NAME, true);
    }
}
