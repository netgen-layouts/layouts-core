<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

final class ItemView extends View implements ItemViewInterface
{
    public function getItem()
    {
        return $this->parameters['item'];
    }

    public function getViewType()
    {
        return $this->parameters['view_type'];
    }

    public function getIdentifier()
    {
        return 'item_view';
    }

    public function jsonSerialize()
    {
        return array(
            'value' => $this->getItem()->getValue(),
            'valueType' => $this->getItem()->getValueType(),
            'viewType' => $this->getViewType(),
        );
    }
}
