<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Page\Block;

class BlockView extends View implements BlockViewInterface
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     */
    public function __construct(Block $block)
    {
        $this->valueObject = $block;
        $this->internalParameters['block'] = $block;
    }

    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function getBlock()
    {
        return $this->valueObject;
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
