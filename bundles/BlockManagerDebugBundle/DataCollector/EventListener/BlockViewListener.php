<?php

namespace Netgen\Bundle\BlockManagerDebugBundle\DataCollector\EventListener;

use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerDebugBundle\DataCollector\BlockManagerDataCollector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BlockViewListener implements EventSubscriberInterface
{
    const BLACKLISTED_CONTEXTS = array(
        ViewInterface::CONTEXT_API,
        ViewInterface::CONTEXT_ADMIN,
    );

    /**
     * @var \Netgen\Bundle\BlockManagerDebugBundle\DataCollector\BlockManagerDataCollector
     */
    protected $dataCollector;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\BlockManagerDebugBundle\DataCollector\BlockManagerDataCollector $dataCollector
     */
    public function __construct(BlockManagerDataCollector $dataCollector)
    {
        $this->dataCollector = $dataCollector;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(BlockManagerEvents::BUILD_VIEW => array('onBuildView', -65535));
    }

    /**
     * Includes results built from all block collections, if specified so.
     *
     * @param \Netgen\BlockManager\Event\CollectViewParametersEvent $event
     */
    public function onBuildView(CollectViewParametersEvent $event)
    {
        $view = $event->getView();

        if (!$event->getView() instanceof BlockViewInterface) {
            return;
        }

        if (in_array($view->getContext(), self::BLACKLISTED_CONTEXTS, true)) {
            return;
        }

        $this->dataCollector->collectBlockView($view);
    }
}
