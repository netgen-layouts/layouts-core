<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

final class LayoutView extends View implements LayoutViewInterface
{
    public function getLayout()
    {
        return $this->parameters['layout'];
    }

    public function getIdentifier()
    {
        return 'layout_view';
    }

    public function jsonSerialize()
    {
        return array(
            'layoutId' => $this->getLayout()->getId(),
        );
    }
}
