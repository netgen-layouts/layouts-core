<?php

namespace Netgen\BlockManager\Tests\View\Stubs;

use Netgen\BlockManager\View\CacheableViewInterface;
use Netgen\BlockManager\View\CacheableViewTrait;

class CacheableView extends View implements CacheableViewInterface
{
    use CacheableViewTrait;
}
