<?php

namespace Netgen\BlockManager\View\Matcher\Layout;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\LayoutInfoViewInterface;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use Netgen\BlockManager\View\ViewInterface;

class Shared implements MatcherInterface
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
        if (!$view instanceof LayoutViewInterface && !$view instanceof LayoutInfoViewInterface) {
            return false;
        }

        if (empty($config)) {
            return true;
        }

        return $view->getLayout()->isShared() === array_values($config)[0];
    }
}
