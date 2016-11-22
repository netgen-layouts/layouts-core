<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\BlockView;

use Netgen\BlockManager\View\View\BlockViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\Event\BlockManagerEvents;

class GetDynamicParametersListener implements EventSubscriberInterface
{
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
     * Includes block definition dynamic parameters into block view if specified.
     *
     * @param \Netgen\BlockManager\Event\CollectViewParametersEvent $event
     */
    public function onBuildView(CollectViewParametersEvent $event)
    {
        $view = $event->getView();
        if (!$view instanceof BlockViewInterface) {
            return;
        }

        $blockDefinition = $view->getBlock()->getBlockDefinition();
        $event->getParameterBag()->set(
            'dynamic_parameters',
            $blockDefinition->getDynamicParameters($view->getBlock())
        );
    }
}
