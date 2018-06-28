<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\View\ViewInterface;

interface BlockViewInterface extends ViewInterface
{
    /**
     * Returns the block.
     */
    public function getBlock(): Block;
}
