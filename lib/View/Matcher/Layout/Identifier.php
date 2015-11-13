<?php

namespace Netgen\BlockManager\View\Matcher\Layout;

use Netgen\BlockManager\View\Matcher\Matcher;
use Netgen\BlockManager\View\LayoutViewInterface;
use Netgen\BlockManager\View\ViewInterface;

class Identifier extends Matcher
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
        if (!$view instanceof LayoutViewInterface) {
            return false;
        }

        return in_array($view->getLayout()->getIdentifier(), $this->config);
    }
}
