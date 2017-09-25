<?php

namespace Netgen\BlockManager\View\Matcher\Layout;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the layout in the provided view
 * has a layout type with the value specified in the configuration.
 */
final class Type implements MatcherInterface
{
    public function match(ViewInterface $view, array $config)
    {
        if (!$view instanceof LayoutViewInterface) {
            return false;
        }

        return in_array($view->getLayout()->getLayoutType()->getIdentifier(), $config, true);
    }
}
