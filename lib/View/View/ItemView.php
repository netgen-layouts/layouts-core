<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\View\View;

final class ItemView extends View implements ItemViewInterface
{
    public function getItem(): ItemInterface
    {
        return $this->parameters['item'];
    }

    public function getViewType(): string
    {
        return $this->parameters['view_type'];
    }

    public function getIdentifier(): string
    {
        return 'item_view';
    }
}
