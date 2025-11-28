<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\View\View;

final class LayoutView extends View implements LayoutViewInterface
{
    public string $identifier {
        get => 'layout';
    }

    public Layout $layout {
        get => $this->getParameter('layout');
    }

    public function __construct(Layout $layout)
    {
        $this->addInternalParameter('layout', $layout);
    }
}
