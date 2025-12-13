<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use function str_starts_with;

final class SetIsAdminRequestListener implements EventSubscriberInterface
{
    public const string ADMIN_FLAG_NAME = 'nglayouts_is_admin_request';

    private const string ADMIN_ROUTE_PREFIX = 'nglayouts_admin_';

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [RequestEvent::class => ['onKernelRequest', 30]];
    }

    /**
     * Sets the self::ADMIN_FLAG_NAME flag if this is a request in admin interface.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->getString('_route');
        if (!str_starts_with($currentRoute, self::ADMIN_ROUTE_PREFIX)) {
            return;
        }

        $request->attributes->set(self::ADMIN_FLAG_NAME, true);

        $adminEvent = new AdminMatchEvent($event->getRequest(), $event->getRequestType());
        $this->eventDispatcher->dispatch($adminEvent);
    }
}
