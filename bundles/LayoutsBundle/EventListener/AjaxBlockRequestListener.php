<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use function preg_replace;

final class AjaxBlockRequestListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // Must happen before ContextListener
        return [KernelEvents::REQUEST => ['onKernelRequest', 10]];
    }

    /**
     * Removes the "page" query parameter from the AJAX block request URI
     * in order to remove the need to hash the page number, which greatly
     * simplifies generating URIs to single pages of AJAX block request.
     *
     * If we were to hash the page parameter too, JavaScript code would not
     * be able to generate the link to a single page simply by changing the
     * page number.
     *
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     */
    public function onKernelRequest($event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->attributes->get('_route') !== 'nglayouts_ajax_block') {
            return;
        }

        if ($request->attributes->has('nglContextUri')) {
            return;
        }

        // This is a naive implementation which removes the need to deconstruct
        // the URI with parse_url/parse_str and then rebuilding it, just to remove
        // a single query parameter with a known name and format.
        $requestUri = preg_replace(
            ['/&page=\d+/', '/\?page=\d+&/', '/\?page=\d+/'],
            ['', '?', ''],
            $request->getRequestUri(),
        );

        $request->attributes->set('nglContextUri', $requestUri);
    }
}
