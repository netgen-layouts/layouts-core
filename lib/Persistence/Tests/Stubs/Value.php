<?php

namespace Netgen\BlockManager\Persistence\Tests\Stubs;

use Netgen\BlockManager\Persistence\Values\Value as BaseValue;

class Value extends BaseValue
{
    /**
     * @var mixed
     */
    public $someProperty;

    /**
     * @var mixed
     */
    public $someOtherProperty;
}
