<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\EventListener\LayoutView;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RelatedLayoutsCountListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    public static function getSubscribedEvents()
    {
        return array(BlockManagerEvents::BUILD_VIEW => 'onBuildView');
    }

    /**
     * Injects the number of layouts connected to the shared layout
     * provided by the event.
     *
     * @param \Netgen\BlockManager\Event\CollectViewParametersEvent $event
     */
    public function onBuildView(CollectViewParametersEvent $event)
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
