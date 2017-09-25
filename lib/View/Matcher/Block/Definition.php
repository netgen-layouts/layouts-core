<?php

namespace Netgen\BlockManager\View\Matcher\Block;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the block in the provided view
 * has a definition identifier with the value specified in the configuration.
 */
final class Definition implements MatcherInterface
{
    public function match(ViewInterface $view, array $config)
    {
        if (!$view instanceof BlockViewInterface) {
            return false;
        }

        return in_array($view->getBlock()->getDefinition()->getIdentifier(), $config, true);
    }
}
