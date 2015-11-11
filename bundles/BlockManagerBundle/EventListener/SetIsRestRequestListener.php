<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SetIsRestRequestListener implements EventSubscriberInterface
{
    const REST_API_FLAG_NAME = 'ngbm_is_rest';
    const REST_API_ROUTE_PREFIX = 'netgen_block_manager_api_';

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
     * Sets the {@link self::REST_API_FLAG_NAME} flag if this is a REST request.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route');
        if (stripos($currentRoute, self::REST_API_ROUTE_PREFIX) !== 0) {
            return;
        }

        $request->attributes->set(self::REST_API_FLAG_NAME, true);
    }
}
