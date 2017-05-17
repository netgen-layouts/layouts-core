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
    protected $layoutService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     */
    public function __construct(LayoutService $layoutService)
    {
        $this->layoutService = $layoutService;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(BlockManagerEvents::BUILD_VIEW => 'onBuildView');
    }

    /**
     * Injects the rule count to the layout.
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
