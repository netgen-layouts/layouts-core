<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Item;

use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\ItemViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

/**
 * This matcher matches if the view type of the item in the provided view
 * has a value specified in the configuration.
 */
final class ViewType implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof ItemViewInterface) {
            return false;
        }

        return in_array($view->getViewType(), $config, true);
    }
}
