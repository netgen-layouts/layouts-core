<?php

namespace Netgen\BlockManager\Tests\Stubs;

use Netgen\BlockManager\Value as BaseValue;

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
