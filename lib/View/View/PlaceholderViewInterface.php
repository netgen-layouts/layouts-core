<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\ViewInterface;

interface PlaceholderViewInterface extends ViewInterface
{
    /**
     * Returns the placeholder.
     *
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder
     */
    public function getPlaceholder();

    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block
     */
    public function getBlock();
}
