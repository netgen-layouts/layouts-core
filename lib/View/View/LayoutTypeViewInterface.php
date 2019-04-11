<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\View\ViewInterface;

interface LayoutTypeViewInterface extends ViewInterface
{
    /**
     * Returns the layout type.
     */
    public function getLayoutType(): LayoutTypeInterface;
}
