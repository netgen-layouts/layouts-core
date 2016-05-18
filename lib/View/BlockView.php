<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Page\Block;

class BlockView extends View implements BlockViewInterface
{
    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function getBlock()
    {
        return $this->value;
    }

    /**
     * Sets the block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     */
    public function setBlock(Block $block)
    {
        $this->value = $block;
        $this->internalParameters['block'] = $this->value;
    }

    /**
     * Returns the view alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'block_view';
    }
}
