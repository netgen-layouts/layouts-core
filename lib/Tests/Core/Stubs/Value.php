<?php

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\Core\Values\Value as BaseValue;

class Value extends BaseValue
{
    /**
     * @var int
     */
    public $status;

    /**
     * @var mixed
     */
    public $someProperty;

    /**
     * @var mixed
     */
    public $someOtherProperty;
}
