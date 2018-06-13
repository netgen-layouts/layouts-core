<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

final class PlaceholderView extends View implements PlaceholderViewInterface
{
    public function getPlaceholder()
    {
        return $this->parameters['placeholder'];
    }

    public function getBlock()
    {
        return $this->parameters['block'];
    }

    public function getIdentifier()
    {
        return 'placeholder_view';
    }
}
