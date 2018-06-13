<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Browser\Item\Layout\LayoutInterface;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

final class LayoutId implements ColumnValueProviderInterface
{
    public function getValue(ItemInterface $item)
    {
        if (!$item instanceof LayoutInterface) {
            return null;
        }

        return $item->getLayout()->getId();
    }
}
