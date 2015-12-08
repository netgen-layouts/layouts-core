<?php

namespace Netgen\BlockManager\EventListener;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Event\View\ViewEvents;
use Netgen\BlockManager\View\LayoutViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class APILayoutViewZonesListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

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

        $zones = array();
        $layout = $view->getLayout();
        $layoutConfig = $this->configuration->getLayoutConfig($layout->getIdentifier());

        foreach ($layout->getZones() as $zoneIdentifier => $zone) {
            $allowedBlocks = true;

            if (isset($layoutConfig['zones'][$zoneIdentifier])) {
                $zoneConfig = $layoutConfig['zones'][$zoneIdentifier];
                if (!empty($zoneConfig['allowed_blocks'])) {
                    $allowedBlocks = $zoneConfig['allowed_blocks'];
                }
            }

            $zones[] = array(
                'identifier' => $zoneIdentifier,
                'allowed_blocks' => $allowedBlocks,
            );
        }

        $event->getParameterBag()->set('zones', $zones);
    }
}
