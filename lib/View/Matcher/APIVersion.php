<?php

namespace Netgen\BlockManager\View\Matcher;

use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the view has an `api_version`
 * parameter with the value specified in the configuration.
 */
class APIVersion implements MatcherInterface
{
    public function match(ViewInterface $view, array $config)
    {
        if (!$view->hasParameter('api_version')) {
            return false;
        }

        return in_array($view->getParameter('api_version'), $config, true);
    }
}
