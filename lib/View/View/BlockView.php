<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\CacheableViewTrait;
use Netgen\BlockManager\View\View;

final class BlockView extends View implements BlockViewInterface
{
    use CacheableViewTrait;

    public function getBlock()
    {
        return $this->parameters['block'];
    }

    public function getIdentifier()
    {
        return 'block_view';
    }
}
