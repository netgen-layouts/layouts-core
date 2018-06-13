<?php

declare(strict_types=1);

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
}
