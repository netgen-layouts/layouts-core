<?php

declare(strict_types=1);

namespace Netgen\Layouts\Browser\Item\Layout;

use Netgen\Layouts\API\Values\Layout\Layout;

interface LayoutInterface
{
    /**
     * Returns the layout.
     */
    public function getLayout(): Layout;
}
