<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache;

use Netgen\BlockManager\View\CacheableViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\EventListener\SessionListener;

/**
 * On Symfony 3.4+, some Cache-Control headers are forced when session is present
 * which make the AJAX and ESI block caching useless for logged in users,
 * so we disable the default behaviour if the view is coming from Netgen Layouts and is
 * cacheable.
 *
 * https://github.com/symfony/symfony/issues/25736
 *
 * @todo Investigate solutions for Symfony 4.1+, where inner session listener
 * has some other logic.
 */
final class CacheableViewSessionListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\HttpKernel\EventListener\SessionListener
     */
    private $innerListener;

    public function __construct(SessionListener $innerListener)
    {
        $this->innerListener = $innerListener;
    }

    public static function getSubscribedEvents()
    {
        return SessionListener::getSubscribedEvents();
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        return $this->innerListener->onKernelRequest($event);
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $view = $event->getRequest()->attributes->get('ngbmView');
        if (!$view instanceof CacheableViewInterface || !$view->isCacheable()) {
            $this->innerListener->onKernelResponse($event);
        }

        // Do nothing, see class description
    }
}
