<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\ViewInterface;

interface PlaceholderViewInterface extends ViewInterface
{
    /**
     * Returns the placeholder.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Placeholder
     */
    public function getPlaceholder();

    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function getBlock();
}
