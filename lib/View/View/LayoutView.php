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

    public function __construct(
        public private(set) Layout $layout,
    ) {
        $this->addInternalParameter('layout', $this->layout);
    }
}
