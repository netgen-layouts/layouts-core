<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache;

use Netgen\BlockManager\View\CacheableViewInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * On Symfony 3.4+, some Cache-Control headers are forced when session is present
 * which make the AJAX and ESI block caching useless for logged in users,
 * so we remove them here.
 *
 * https://github.com/symfony/symfony/issues/25736
 */
final class CacheableViewSessionListener implements EventSubscriberInterface
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            // Needs to run after SessionListener from Symfony 3.4+
            KernelEvents::RESPONSE => array('onKernelResponse', -1010),
        );
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        $view = $request->attributes->get('ngbmView');
        if (!$view instanceof CacheableViewInterface) {
            return;
        }

        $session = $this->container->has('initialized_session') ?
            $this->container->get('initialized_session') :
            $request->getSession();

        if (!$session) {
            return;
        }

        if ($session->isStarted() || ($session instanceof Session && $session->hasBeenStarted())) {
            $response = $event->getResponse();

            $response->setPublic();
            $response->headers->removeCacheControlDirective('must-revalidate');
            $response->headers->removeCacheControlDirective('max-age');
        }
    }
}
