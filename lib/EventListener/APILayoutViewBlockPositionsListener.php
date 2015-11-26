<?php

namespace Netgen\BlockManager\EventListener;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\LayoutViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class APILayoutViewBlockPositionsListener implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(ViewEvents::BUILD_VIEW => 'onBuildView');
    }

    /**
     * Adds a list of zones and allowed blocks to API layout views.
     *
     * Used in layout view serialization process.
     *
     * @param \Netgen\BlockManager\Event\View\CollectViewParametersEvent $event
     */
    public function onBuildView(CollectViewParametersEvent $event)
    {
        $view = $event->getView();
        if (!$view instanceof LayoutViewInterface || $view->getContext() !== 'api') {
            return;
        }

        $positions = array();

        foreach ($view->getLayout()->getZones() as $zone) {
            $positions[] = array(
                'zone' => $zone->getIdentifier(),
                'blocks' => array_map(
                    function(Block $block) {
                        return $block->getId();
                    },
                    $zone->getBlocks()
                ),
            );
        }

        $event->getParameterBag()->set('positions', $positions);
    }
}
