<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\BlockView;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\View\BlockViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Event\View\ViewEvents;

class GetDynamicParametersListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * @var array
     */
    protected $enabledContexts;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     * @param array $enabledContexts
     */
    public function __construct(
        BlockDefinitionRegistryInterface $blockDefinitionRegistry,
        array $enabledContexts = array()
    ) {
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
        $this->enabledContexts = $enabledContexts;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(ViewEvents::BUILD_VIEW => 'onBuildView');
    }

    /**
     * Includes block definition dynamic parameters into block view if specified.
     *
     * @param \Netgen\BlockManager\Event\View\CollectViewParametersEvent $event
     */
    public function onBuildView(CollectViewParametersEvent $event)
    {
        $view = $event->getView();
        if (!$view instanceof BlockViewInterface) {
            return;
        }

        if (!in_array($view->getContext(), $this->enabledContexts)) {
            return;
        }

        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
            $view->getBlock()->getDefinitionIdentifier()
        );

        $event->getParameterBag()->add(
            $blockDefinition->getDynamicParameters($view->getBlock(), $view->getParameters())
        );
    }
}
