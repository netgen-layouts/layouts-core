<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Matcher\Item;

use Netgen\BlockManager\Item\NullCmsItem;
use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\ItemViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the value type of the item in the provided view
 * has a value specified in the configuration.
 */
final class ValueType implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof ItemViewInterface) {
            return false;
        }

        $item = $view->getItem();
        if ($item instanceof NullCmsItem) {
            return in_array('null', $config, true);
        }

        return in_array($item->getValueType(), $config, true);
    }
}
