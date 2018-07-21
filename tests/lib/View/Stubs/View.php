<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Stubs;

use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\View as BaseView;

final class View extends BaseView
{
    public function __construct(Value $value)
    {
        $this->parameters['value'] = $value;
    }

    public static function getIdentifier(): string
    {
        return 'stub';
    }
}
