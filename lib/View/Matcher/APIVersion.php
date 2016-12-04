<?php

namespace Netgen\BlockManager\View\Matcher;

use Netgen\BlockManager\View\ViewInterface;

class APIVersion implements MatcherInterface
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
        if (!$view->hasParameter('api_version')) {
            return false;
        }

        return in_array($view->getParameter('api_version'), $config, true);
    }
}
