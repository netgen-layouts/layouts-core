<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\View\ViewInterface;

interface PlaceholderViewInterface extends ViewInterface
{
    /**
     * Returns the placeholder.
     */
    public function getPlaceholder(): Placeholder;

    /**
     * Returns the block.
     */
    public function getBlock(): Block;
}
