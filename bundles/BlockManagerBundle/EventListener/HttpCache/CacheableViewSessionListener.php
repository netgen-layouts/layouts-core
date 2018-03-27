<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache;

use Netgen\BlockManager\View\CacheableViewInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\EventListener\SessionListener;
use Symfony\Component\HttpKernel\Kernel;

/**
 * On Symfony 3.4+, some Cache-Control headers are forced when session is present
 * which make the AJAX and ESI block caching useless for logged in users,
 * so we disable the default behaviour if the view is coming from Netgen Layouts and is
 * cacheable.
 *
 * https://github.com/symfony/symfony/issues/25736
 */
final class CacheableViewSessionListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\HttpKernel\EventListener\SessionListener
     */
    private $innerListener;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    public function __construct(SessionListener $innerListener, ContainerInterface $container = null)
    {
        $this->innerListener = $innerListener;
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return SessionListener::getSubscribedEvents();
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        return $this->innerListener->onKernelRequest($event);
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $session = $this->getSession($event);
        if (!$session) {
            return;
        }

        $view = $event->getRequest()->attributes->get('ngbmView');
        if ($view instanceof CacheableViewInterface && $view->isCacheable()) {
            if (Kernel::VERSION_ID >= 40100 && $session->isStarted()) {
                $session->save();
            }

            return;
        }

        $this->innerListener->onKernelResponse($event);
    }

    private function getSession(FilterResponseEvent $event)
    {
        if ($this->container && $this->container->has('initialized_session')) {
            return $this->container->get('initialized_session');
        }

        return $event->getRequest()->getSession();
    }
}
