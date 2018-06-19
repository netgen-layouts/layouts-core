<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache;

use Netgen\BlockManager\View\CacheableViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\EventListener\SessionListener;

/**
 * On Symfony 3.4 and 4.0, some Cache-Control headers are forced when session is present
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

    public function __construct(SessionListener $innerListener)
    {
        $this->innerListener = $innerListener;
    }

    public static function getSubscribedEvents(): array
    {
        return SessionListener::getSubscribedEvents();
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $this->innerListener->onKernelRequest($event);
    }

    public function onFinishRequest(FinishRequestEvent $event): void
    {
        if (method_exists($this->innerListener, 'onFinishRequest')) {
            $this->innerListener->onFinishRequest($event);
        }
    }

    public function onKernelResponse(FilterResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $view = $event->getRequest()->attributes->get('ngbmView');
        if ($view instanceof CacheableViewInterface && $view->isCacheable()) {
            return;
        }

        $this->innerListener->onKernelResponse($event);
    }
}
