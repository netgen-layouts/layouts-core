<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache;

use Netgen\BlockManager\View\CacheableViewInterface;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CacheableViewListener implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => 'onView',
            KernelEvents::RESPONSE => array('onKernelResponse', -255),
        );
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
     */
    public function onView(GetResponseForControllerResultEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $controllerResult = $event->getControllerResult();
        if (!$controllerResult instanceof CacheableViewInterface) {
            return;
        }

        $this->setUpCachingHeaders($controllerResult, $controllerResult->getResponse());
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $layoutView = $event->getRequest()->attributes->get('layoutView');
        if (!$layoutView instanceof LayoutViewInterface) {
            return;
        }

        $this->setUpCachingHeaders($layoutView, $event->getResponse());
    }

    /**
     * @param \Netgen\BlockManager\View\CacheableViewInterface $cacheableView
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    protected function setUpCachingHeaders(CacheableViewInterface $cacheableView, Response $response)
    {
        if (!$cacheableView->isCacheable()) {
            return;
        }

        if (!$response->headers->hasCacheControlDirective('s-maxage') || $cacheableView->overwriteHeaders()) {
            $sharedMaxAge = (int) $cacheableView->getSharedMaxAge();
            if ($sharedMaxAge > 0) {
                $response->setSharedMaxAge($sharedMaxAge);
            }
        }
    }
}
