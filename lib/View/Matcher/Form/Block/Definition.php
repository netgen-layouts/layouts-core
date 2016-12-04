<?php

namespace Netgen\BlockManager\View\Matcher\Form\Block;

use Netgen\BlockManager\API\Values\Page\Block;
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

        if (!$view->getForm()->getConfig()->hasOption('block')) {
            return false;
        }

        $block = $view->getForm()->getConfig()->getOption('block');
        if (!$block instanceof Block) {
            return false;
        }

        return in_array($block->getBlockDefinition()->getIdentifier(), $config, true);
    }
}
