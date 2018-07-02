<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView;

use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\HttpCache\ClientInterface;
use Netgen\BlockManager\HttpCache\NullClient;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CacheEnabledListener implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    private $cacheEnabled = true;

    public function __construct(ClientInterface $httpCacheClient)
    {
        $this->cacheEnabled = !$httpCacheClient instanceof NullClient;
    }

    public static function getSubscribedEvents(): array
    {
        return [sprintf('%s.%s', BlockManagerEvents::BUILD_VIEW, 'layout') => 'onBuildView'];
    }

    /**
     * Injects if the HTTP cache clearing is enabled or not.
     */
    public function onBuildView(CollectViewParametersEvent $event): void
    {
        $view = $event->getView();
        if (!$view instanceof LayoutViewInterface) {
            return;
        }

        if ($view->getContext() !== ViewInterface::CONTEXT_ADMIN) {
            return;
        }

        $event->addParameter('http_cache_enabled', $this->cacheEnabled);
    }
}
