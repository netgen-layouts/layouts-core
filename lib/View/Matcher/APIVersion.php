<?php

namespace Netgen\BlockManager\View\Matcher;

use Netgen\BlockManager\View\ViewInterface;

class APIVersion extends Matcher
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
        if (!$view->hasParameter('api_version')) {
            return false;
        }

        return in_array($view->getParameter('api_version'), $this->config);
    }
}
