<?php

namespace Netgen\BlockManager\Tests\View\Stubs;

use Netgen\BlockManager\View\View as BaseView;

class View extends BaseView
{
    public function getIdentifier()
    {
        return 'view';
    }

    public function jsonSerialize()
    {
        return array();
    }
}
