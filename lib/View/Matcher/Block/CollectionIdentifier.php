<?php

namespace Netgen\BlockManager\View\Matcher\Block;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the block in the provided view
 * has a collection_identifier parameter with the provided value.
 */
final class CollectionIdentifier implements MatcherInterface
{
    public function match(ViewInterface $view, array $config)
    {
        if (!$view instanceof BlockViewInterface) {
            return false;
        }

        if (!$view->hasParameter('collection_identifier')) {
            return false;
        }

        return in_array($view->getParameter('collection_identifier'), $config, true);
    }
}
