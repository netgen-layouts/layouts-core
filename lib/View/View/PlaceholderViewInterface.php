<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\View\ViewInterface;

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
