<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\View\ViewInterface;

interface BlockViewInterface extends ViewInterface
{
    /**
     * Returns the block.
     */
    public function getBlock(): Block;
}
