<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

class BlockView extends View implements BlockViewInterface
{
    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function getBlock()
    {
        return $this->parameters['block'];
    }

    /**
     * Returns the view identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'block_view';
    }
}
