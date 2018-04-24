<?php

namespace Netgen\BlockManager\View\Matcher\Layout;

use Netgen\BlockManager\Layout\Type\NullLayoutType;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\LayoutTypeViewInterface;
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
        if (!$view instanceof LayoutViewInterface && !$view instanceof LayoutTypeViewInterface) {
            return false;
        }

        $layoutType = $view instanceof LayoutViewInterface ?
            $view->getLayout()->getLayoutType() :
            $view->getLayoutType();

        if ($layoutType instanceof NullLayoutType) {
            return in_array('null', $config, true);
        }

        return in_array($layoutType->getIdentifier(), $config, true);
    }
}
