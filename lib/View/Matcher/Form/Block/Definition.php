<?php

namespace Netgen\BlockManager\View\Matcher\Form\Block;

use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\FormViewInterface;
use Netgen\BlockManager\View\ViewInterface;

class Definition implements MatcherInterface
{
    /**
     * Returns if the view matches the config.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param array $config
     *
     * @return bool
     */
    public function match(ViewInterface $view, array $config)
    {
        if (!$view instanceof FormViewInterface) {
            return false;
        }

        if (!$view->getForm()->getConfig()->hasOption('blockDefinition')) {
            return false;
        }

        $blockDefinition = $view->getForm()->getConfig()->getOption('blockDefinition');
        if (!$blockDefinition instanceof BlockDefinitionInterface) {
            return false;
        }

        return in_array($blockDefinition->getIdentifier(), $config);
    }
}
