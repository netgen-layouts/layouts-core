<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView;

use Netgen\Layouts\Event\BuildViewEvent;
use Netgen\Layouts\HttpCache\ClientInterface;
use Netgen\Layouts\HttpCache\NullClient;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Netgen\Layouts\View\View\RuleViewInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CacheEnabledListener implements EventSubscriberInterface
{
    private bool $cacheEnabled;

    public function __construct(ClientInterface $httpCacheClient)
    {
        $this->cacheEnabled = !$httpCacheClient instanceof NullClient;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BuildViewEvent::getEventName('layout') => 'onBuildView',
            BuildViewEvent::getEventName('rule') => 'onBuildView',
        ];
    }

    /**
     * Injects if the HTTP cache clearing is enabled or not.
     */
    public function onBuildView(BuildViewEvent $event): void
    {
        $view = $event->view;
        if (!$view instanceof LayoutViewInterface && !$view instanceof RuleViewInterface) {
            return;
        }

        if ($view->context !== ViewInterface::CONTEXT_ADMIN) {
            return;
        }

        $event->view->addParameter('http_cache_enabled', $this->cacheEnabled);
    }
}
