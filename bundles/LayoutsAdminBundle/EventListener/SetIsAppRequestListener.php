<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

use function str_starts_with;

final class SetIsAppRequestListener implements EventSubscriberInterface
{
    public const string APP_FLAG_NAME = 'nglayouts_is_app_request';

    private const string APP_ROUTE_PREFIX = 'nglayouts_app_';

    public static function getSubscribedEvents(): array
    {
        return [RequestEvent::class => ['onKernelRequest', 30]];
    }

    /**
     * Sets the self::APP_FLAG_NAME flag if this is a request in layout editing interface.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->getString('_route');
        if (!str_starts_with($currentRoute, self::APP_ROUTE_PREFIX)) {
            return;
        }

        $request->attributes->set(self::APP_FLAG_NAME, true);
    }
}
