<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

final class LayoutTypeView extends View implements LayoutTypeViewInterface
{
    public function getLayoutType()
    {
        return $this->parameters['layoutType'];
    }

    public function getIdentifier()
    {
        return 'layout_view';
    }
}
