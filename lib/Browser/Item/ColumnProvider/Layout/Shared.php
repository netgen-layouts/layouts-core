<?php

namespace Netgen\BlockManager\Browser\Item\ColumnProvider\Layout;

use Netgen\ContentBrowser\Item\ColumnProvider\ColumnValueProviderInterface;
use Netgen\ContentBrowser\Item\ItemInterface;

class Shared implements ColumnValueProviderInterface
{
    public function getValue(ItemInterface $item)
    {
        return $item->getLayout()->isShared() ? 'Yes' : 'No';
    }
}
