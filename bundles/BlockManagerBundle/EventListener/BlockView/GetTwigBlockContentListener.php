<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\BlockView;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\TwigBlockDefinitionInterface;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GetTwigBlockContentListener implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(BlockManagerEvents::RENDER_VIEW => 'onRenderView');
    }

    /**
     * Includes the Twig block content from a Twig block.
     *
     * @param \Netgen\BlockManager\Event\CollectViewParametersEvent $event
     */
    public function onRenderView(CollectViewParametersEvent $event)
    {
        $view = $event->getView();
        if (!$view instanceof BlockViewInterface) {
            return;
        }

        $block = $view->getBlock();
        $blockDefinition = $block->getDefinition();

        if (!$blockDefinition instanceof TwigBlockDefinitionInterface) {
            return;
        }

        $twigContent = $this->getTwigBlockContent(
            $blockDefinition,
            $block,
            $view->getParameters()
        );

        $event->addParameter('twig_content', $twigContent);
    }

    /**
     * Returns the Twig block content from the provided block.
     *
     * @param \Netgen\BlockManager\Block\TwigBlockDefinitionInterface $blockDefinition
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param array $parameters
     *
     * @return string
     */
    protected function getTwigBlockContent(
        TwigBlockDefinitionInterface $blockDefinition,
        Block $block,
        array $parameters = array()
    ) {
        if (!isset($parameters['twig_template'])) {
            return '';
        }

        if (!$parameters['twig_template'] instanceof ContextualizedTwigTemplate) {
            return '';
        }

        return $parameters['twig_template']->renderBlock(
            $blockDefinition->getTwigBlockName($block)
        );
    }
}
