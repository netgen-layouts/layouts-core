<?php

namespace Netgen\BlockManager\Tests\API\Stubs;

use Netgen\BlockManager\API\Values\Value as BaseValue;

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
