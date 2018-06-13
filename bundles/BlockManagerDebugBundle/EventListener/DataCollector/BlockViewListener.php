<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerDebugBundle\EventListener\DataCollector;

use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\Bundle\BlockManagerDebugBundle\DataCollector\BlockManagerDataCollector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class BlockViewListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\Bundle\BlockManagerDebugBundle\DataCollector\BlockManagerDataCollector
     */
    private $dataCollector;

    /**
     * @var string[]
     */
    private $enabledContexts;

    /**
     * @param \Netgen\Bundle\BlockManagerDebugBundle\DataCollector\BlockManagerDataCollector $dataCollector
     * @param string[] $enabledContexts
     */
    public function __construct(BlockManagerDataCollector $dataCollector, array $enabledContexts = [])
    {
        $this->dataCollector = $dataCollector;
        $this->enabledContexts = $enabledContexts;
    }

    public static function getSubscribedEvents()
    {
        return [BlockManagerEvents::BUILD_VIEW => ['onBuildView', -65535]];
    }

    /**
     * Includes results built from all block collections, if specified so.
     *
     * @param \Netgen\BlockManager\Event\CollectViewParametersEvent $event
     */
    public function onBuildView(CollectViewParametersEvent $event)
    {
        $view = $event->getView();

        if (!$view instanceof BlockViewInterface) {
            return;
        }

        if (!in_array($view->getContext(), $this->enabledContexts, true)) {
            return;
        }

        $this->dataCollector->collectBlockView($view);
    }
}
