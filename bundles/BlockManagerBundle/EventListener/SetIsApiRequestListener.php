<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetIsApiRequestListener implements EventSubscriberInterface
{
    public const API_FLAG_NAME = 'ngbm_is_api_request';

    private static $apiRoutePrefix = 'ngbm_api_';

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 30]];
    }

    /**
     * Sets the self::API_FLAG_NAME flag if this is a REST API request.
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route');
        if (mb_stripos($currentRoute, self::$apiRoutePrefix) !== 0) {
            return;
        }

        $request->attributes->set(self::API_FLAG_NAME, true);
    }
}
