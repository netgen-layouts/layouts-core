<?php

namespace Netgen\BlockManager\Tests\Persistence\Stubs;

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
