<?php

namespace Netgen\BlockManager\Tests\View\Stubs;

use Netgen\BlockManager\View\CacheableViewInterface;
use Netgen\BlockManager\View\CacheableViewTrait;

final class CacheableView extends View implements CacheableViewInterface
{
    use CacheableViewTrait;
}
