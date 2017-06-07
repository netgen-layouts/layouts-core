<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\EventListener;

use Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent;
use Netgen\Bundle\BlockManagerAdminBundle\Event\BlockManagerAdminEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SetIsAdminRequestListener implements EventSubscriberInterface
{
    const ADMIN_FLAG_NAME = 'ngbm_is_admin_request';
    const ADMIN_ROUTE_PREFIX = 'ngbm_admin_';

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

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
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route');
        if (mb_stripos($currentRoute, self::ADMIN_ROUTE_PREFIX) !== 0) {
            return;
        }

        $request->attributes->set(self::ADMIN_FLAG_NAME, true);

        $event = new AdminMatchEvent($event->getRequest(), $event->getRequestType());
        $this->eventDispatcher->dispatch(BlockManagerAdminEvents::ADMIN_MATCH, $event);
    }
}
