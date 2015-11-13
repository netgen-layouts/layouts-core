<?php

namespace Netgen\BlockManager\View\Matcher\Block;

use Netgen\BlockManager\View\Matcher\Matcher;
use Netgen\BlockManager\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewInterface;

class DefinitionIdentifier extends Matcher
{
    /**
     * Returns if the view matches the config.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return bool
     */
    public function match(ViewInterface $view)
    {
        if (!$view instanceof BlockViewInterface) {
            return false;
        }

        return in_array($view->getBlock()->getDefinitionIdentifier(), $this->config);
    }
}
