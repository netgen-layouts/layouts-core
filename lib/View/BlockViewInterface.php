<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Page\Block;

interface BlockViewInterface extends ViewInterface
{
    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function getBlock();

    /**
     * Sets the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     */
    public function setBlock(Block $block);
}
