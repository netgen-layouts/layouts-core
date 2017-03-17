<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\CacheableViewTrait;
use Netgen\BlockManager\View\View;

class BlockView extends View implements BlockViewInterface
{
    use CacheableViewTrait;

    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
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
