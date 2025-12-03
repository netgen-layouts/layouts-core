<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Event\BuildViewEvent;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RelatedLayoutsCountListener implements EventSubscriberInterface
{
    public function __construct(
        private LayoutService $layoutService,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [BuildViewEvent::getEventName('layout') => 'onBuildView'];
    }

    /**
     * Injects the number of layouts connected to the shared layout
     * provided by the event.
     */
    public function onBuildView(BuildViewEvent $event): void
    {
        $view = $event->view;
        if (!$view instanceof LayoutViewInterface) {
            return;
        }

        if ($view->context !== ViewInterface::CONTEXT_ADMIN) {
            return;
        }

        $relatedLayoutsCount = 0;
        if ($view->layout->isShared && $view->layout->isPublished) {
            $relatedLayoutsCount = $this->layoutService->getRelatedLayoutsCount($view->layout);
        }

        $event->view->addParameter('related_layouts_count', $relatedLayoutsCount);
    }
}
