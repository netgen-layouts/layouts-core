<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\View\ViewInterface;

interface LayoutViewInterface extends ViewInterface
{
    /**
     * Returns the layout.
     */
    public function getLayout(): Layout;
}
