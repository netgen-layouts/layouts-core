<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\CacheableViewInterface;
use Netgen\BlockManager\View\ViewInterface;

interface BlockViewInterface extends ViewInterface, CacheableViewInterface
{
    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function getBlock();
}
