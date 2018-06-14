<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Stubs;

use Netgen\BlockManager\View\View as BaseView;

class View extends BaseView
{
    public function getIdentifier(): string
    {
        return 'view';
    }
}
