<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Page\Block;

class BlockView extends View implements BlockViewInterface
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\Block
     */
    protected $block;

    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Sets the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     */
    public function setBlock(Block $block)
    {
        $this->block = $block;
        $this->internalParameters['block'] = $this->block;
    }
}
