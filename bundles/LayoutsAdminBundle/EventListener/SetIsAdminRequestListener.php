<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent;
use Netgen\Bundle\LayoutsAdminBundle\Event\LayoutsAdminEvents;
use Netgen\Layouts\Utils\BackwardsCompatibility\EventDispatcherProxy;
use Netgen\Layouts\Utils\BackwardsCompatibility\MainRequestEventTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

use function str_starts_with;

final class SetIsAdminRequestListener implements EventSubscriberInterface
{
    use MainRequestEventTrait;

    public const ADMIN_FLAG_NAME = 'nglayouts_is_admin_request';

    private const ADMIN_ROUTE_PREFIX = 'nglayouts_admin_';

    private EventDispatcherProxy $eventDispatcher;

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
        if (!$this->isMainRequest($event)) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route', '');
        if (!str_starts_with($currentRoute, self::ADMIN_ROUTE_PREFIX)) {
            return;
        }

        $request->attributes->set(self::ADMIN_FLAG_NAME, true);

        $adminEvent = new AdminMatchEvent($event->getRequest(), $event->getRequestType());
        $this->eventDispatcher->dispatch($adminEvent, LayoutsAdminEvents::ADMIN_MATCH);
    }
}
