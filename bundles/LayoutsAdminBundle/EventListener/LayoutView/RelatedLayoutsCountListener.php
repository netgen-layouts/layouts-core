<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\EventListener\LayoutView;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function sprintf;

final class RelatedLayoutsCountListener implements EventSubscriberInterface
{
    private LayoutService $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    public static function getSubscribedEvents(): array
    {
        return [sprintf('%s.%s', LayoutsEvents::BUILD_VIEW, 'layout') => 'onBuildView'];
    }

    /**
     * Injects the number of layouts connected to the shared layout
     * provided by the event.
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

        $layout = $view->getLayout();

        $relatedLayoutsCount = 0;
        if ($layout->isShared() && $layout->isPublished()) {
            $relatedLayoutsCount = $this->layoutService->getRelatedLayoutsCount($layout);
        }

        $event->addParameter('related_layouts_count', $relatedLayoutsCount);
    }
}
