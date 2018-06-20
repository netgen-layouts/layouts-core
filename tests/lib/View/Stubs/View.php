<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Stubs;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\View as BaseView;

class View extends BaseView
{
    public function __construct(Value $value)
    {
        $this->parameters['value'] = $value;
    }

    public function getIdentifier(): string
    {
        return 'stub';
    }
}
