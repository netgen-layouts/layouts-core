<?php

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

    public function jsonSerialize()
    {
        return array(
            'layoutType' => $this->getLayoutType()->getIdentifier(),
        );
    }
}
