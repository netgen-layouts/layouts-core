<?php

namespace Netgen\Bundle\BlockManagerBundle\Browser\Item\Serializer;

use Netgen\ContentBrowser\Item\ItemInterface;
use Netgen\ContentBrowser\Item\Serializer\ItemSerializerHandlerInterface;

class LayoutSerializerHandler implements ItemSerializerHandlerInterface
{
    /**
     * Returns if the item is selectable.
     *
     * @param \Netgen\ContentBrowser\Item\ItemInterface $item
     *
     * @return bool
     */
    public function isSelectable(ItemInterface $item)
    {
        return true;
    }
}
