<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Browser\Item\Layout;

use Netgen\BlockManager\API\Values\Layout\Layout;

interface LayoutInterface
{
    /**
     * Returns the layout.
     */
    public function getLayout(): Layout;
}
