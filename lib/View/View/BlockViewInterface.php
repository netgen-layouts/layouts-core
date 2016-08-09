<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\ViewInterface;

interface BlockViewInterface extends ViewInterface
{
    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function getBlock();
}
