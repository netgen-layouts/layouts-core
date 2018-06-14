<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\EventListener;

use Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent;
use Netgen\Bundle\BlockManagerAdminBundle\Event\BlockManagerAdminEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetIsAdminRequestListener implements EventSubscriberInterface
{
    public const ADMIN_FLAG_NAME = 'ngbm_is_admin_request';

    private static $adminRoutePrefix = 'ngbm_admin_';

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 30]];
    }

    /**
     * Sets the self::ADMIN_FLAG_NAME flag if this is a request in admin interface.
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route');
        if (mb_stripos($currentRoute, self::$adminRoutePrefix) !== 0) {
            return;
        }

        $request->attributes->set(self::ADMIN_FLAG_NAME, true);

        $event = new AdminMatchEvent($event->getRequest(), $event->getRequestType());
        $this->eventDispatcher->dispatch(BlockManagerAdminEvents::ADMIN_MATCH, $event);
    }
}
