<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

use function str_starts_with;

final class SetIsApiRequestListener implements EventSubscriberInterface
{
    public const string API_FLAG_NAME = 'nglayouts_is_app_api_request';

    private const string API_ROUTE_PREFIX = 'nglayouts_app_api_';

    public static function getSubscribedEvents(): array
    {
        return [RequestEvent::class => ['onKernelRequest', 30]];
    }

    /**
     * Sets the self::API_FLAG_NAME flag if this is a REST API request.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route', '');
        if (!str_starts_with($currentRoute, self::API_ROUTE_PREFIX)) {
            return;
        }

        $request->attributes->set(self::API_FLAG_NAME, true);
    }
}
