<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Layout;

use Netgen\Layouts\Layout\Type\NullLayoutType;
use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\LayoutTypeViewInterface;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

/**
 * This matcher matches if the layout in the provided view
 * has a layout type with the value specified in the configuration.
 */
final class Type implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
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
