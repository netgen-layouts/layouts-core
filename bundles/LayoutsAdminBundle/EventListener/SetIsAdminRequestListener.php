<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent;
use Netgen\Bundle\LayoutsAdminBundle\Event\LayoutsAdminEvents;
use Netgen\Layouts\Utils\BackwardsCompatibility\EventDispatcherProxy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use function mb_stripos;

final class SetIsAdminRequestListener implements EventSubscriberInterface
{
    public const ADMIN_FLAG_NAME = 'nglayouts_is_admin_request';

    private const ADMIN_ROUTE_PREFIX = 'nglayouts_admin_';

    /**
     * @var \Netgen\Layouts\Utils\BackwardsCompatibility\EventDispatcherProxy
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = new EventDispatcherProxy($eventDispatcher);
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 30]];
    }

    /**
     * Sets the self::ADMIN_FLAG_NAME flag if this is a request in admin interface.
     *
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     */
    public function onKernelRequest($event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route', '');
        if (mb_stripos($currentRoute, self::ADMIN_ROUTE_PREFIX) !== 0) {
            return;
        }

        $request->attributes->set(self::ADMIN_FLAG_NAME, true);

        $adminEvent = new AdminMatchEvent($event->getRequest(), $event->getRequestType());
        $this->eventDispatcher->dispatch($adminEvent, LayoutsAdminEvents::ADMIN_MATCH);
    }
}
