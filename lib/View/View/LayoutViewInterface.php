<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\View\ViewInterface;

interface LayoutViewInterface extends ViewInterface
{
    /**
     * Returns the layout.
     */
    public function getLayout(): Layout;
}
