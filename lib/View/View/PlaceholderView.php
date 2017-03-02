<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

class PlaceholderView extends View implements PlaceholderViewInterface
{
    /**
     * Returns the placeholder.
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder
     */
    public function getPlaceholder()
    {
        return $this->parameters['placeholder'];
    }

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
        return 'placeholder_view';
    }
}
